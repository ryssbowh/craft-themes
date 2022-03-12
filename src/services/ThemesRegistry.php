<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Theme;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\ThemeEvent;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\jobs\InstallThemeData;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\base\PluginInterface;
use craft\events\RegisterTemplateRootsEvent;
use craft\helpers\Queue;
use craft\models\Site;


class ThemesRegistry extends Service
{   
    const EVENT_THEME_SET = 'themes.set';
    const THEMES_WEBROOT = '@webroot/themes/';

    /**
     * @var ThemeInterface[]
     */
    protected $themes;

    /**
     * @var ?ThemeInterface
     */
    protected $currentTheme;

    /**
     * Have the template roots been registered
     * 
     * @var boolean
     */
    public $rootsRegistered = false;

    /**
     * Get theme for the current site
     * 
     * @return ?ThemeInterface
     */
    public function getCurrent(): ?ThemeInterface
    {
        return $this->currentTheme;
    }

    /**
     * Sets current theme
     * 
     * @param  string|ThemeInterface|null $theme
     * @return ?ThemeInterface
     */
    public function setCurrent($theme): ?ThemeInterface
    {
        if (is_string($theme)) {
            $theme = $this->getTheme($theme);
        }
        if ($this->rootsRegistered) {
            throw ThemeException::rootsRegistered($theme);
        }

        $this->currentTheme = $theme;
        if (!$theme) {
            \Craft::info("Theme has been unset", __METHOD__);
            return null;
        }
        \Yii::setAlias('@themePath', $theme->basePath);
        \Yii::setAlias('@themeWeb', '@themesWeb/' . $theme->handle);
        \Yii::setAlias('@themeWebPath', '@themesWebPath/' . $theme->handle);
        \Craft::$app->view->registerTwigExtension(new TwigTheme);
        $this->currentTheme->afterSet();
        $this->triggerEvent(
            self::EVENT_THEME_SET, 
            new ThemeEvent(['theme' => $this->currentTheme])
        );
        \Craft::info("Theme has been set to " . $this->currentTheme->name, __METHOD__);
        return $this->currentTheme;
    }

    /**
     * Register the current theme templates
     * 
     * @param RegisterTemplateRootsEvent $event
     */
    public function registerCurrentThemeTemplates(RegisterTemplateRootsEvent $event)
    {
        $this->rootsRegistered = true;
        if (!$this->currentTheme) {
            return;
        }
        $event->roots[''][] = __DIR__ . '/../templates/front';
        $event->roots[''] = array_merge($this->currentTheme->getTemplatePaths(), $event->roots['']);
        \Craft::info("Theme {$this->currentTheme->name}'s templates have been registered", __METHOD__);
    }

    /**
     * Register the asset bundles of the current theme
     */
    public function registerCurrentThemeBundles()
    {
        if (!$this->currentTheme or !\Craft::$app->request->getIsSiteRequest()) {
            return;
        }
        $path = \Craft::$app->request->getPathInfo();
        $path = $path === '' ? '/' : $path;
        $this->currentTheme->registerAssetBundles($path);
    }

    /**
     * Get all themes
     * 
     * @return ThemeInterface[]
     */
    public function getAll(): array
    {
        if ($this->themes === null) {
            $this->loadThemes();
        }
        return $this->themes;
    }

    /**
     * Get all themes as names
     * 
     * @return string[]
     */
    public function getAsNames(): array
    {
        return array_map(function ($theme) {
            return $theme->name;
        }, $this->getAll());
    }

    /**
     * Get all non partial themes
     * 
     * @param  boolean $asNames
     * @param  boolean $asArrays
     * @return ThemeInterface[]
     */
    public function getNonPartials(bool $asNames = false, bool $asArrays = false): array
    {
        $themes = array_filter($this->getAll(), function ($theme) {
            return !$theme->isPartial();
        });
        if ($asNames) {
            return array_map(function ($theme) {
                return $theme->name;
            }, $themes);
        } elseif ($asArrays) {
            return array_map(function ($theme) {
                return $theme->toArray();
            }, $themes);
        }
        return $themes;
    }

    /**
     * Get all themes that depends on a theme
     * 
     * @param  ThemeInterface $theme
     * @return ThemeInterface[]
     */
    public function getDependencies(ThemeInterface $theme): array
    {
        $dependencies = array_values(array_filter($this->getAll(), function ($theme2) use ($theme) {
            return $theme2->extends == $theme->handle;
        }));
        foreach ($dependencies as $theme2) {
            $dependencies = array_merge($dependencies, $this->getDependencies($theme2));
        }
        return $dependencies;
    }

    /**
     * Get a theme by handle
     * 
     * @param  string $handle
     * @throws ThemeException
     * @return ThemeInterface
     */
    public function getTheme(string $handle): ThemeInterface
    {
        if (isset($this->getAll()[$handle])) {
            return $this->getAll()[$handle];
        }
        throw ThemeException::notDefined($handle);
    }

    /**
     * Does a theme exist
     * 
     * @param  string  $handle
     * @return boolean
     */
    public function hasTheme(string $handle): bool
    {
        return isset($this->getAll()[$handle]);
    }

    /**
     * Install all theme's data
     *
     * @param bool $force
     */
    public function installAll(bool $force = false)
    {
        foreach ($this->all() as $theme) {
            $this->installThemeData($theme, $force);
        }
    }

    /**
     * Uninstall all theme's data
     */
    public function uninstallAll()
    {
        foreach ($this->all() as $theme) {
            $this->uninstallThemeData($theme);
        }
    }

    /**
     * Install a theme
     *
     * @param ThemeInterface $theme
     * @param bool           $force
     */
    public function installThemeData(ThemeInterface $theme, bool $force = false)
    {
        $this->resetThemes();
        if ($force or Themes::$plugin->is(Themes::EDITION_PRO)) {
            Themes::$plugin->layouts->installThemeData($theme);
            $theme->afterThemeInstall();
            ProjectConfigHelper::markDataInstalledForTheme($theme);
        }
    }

    /**
     * Uninstall a theme
     *
     * @return ThemeInterface $theme
     */
    public function uninstallThemeData(ThemeInterface $theme)
    {
        $this->resetThemes();
        Themes::$plugin->rules->flushCache();
        Themes::$plugin->layouts->uninstallThemeData($theme);
        $theme->afterThemeUninstall();
        ProjectConfigHelper::markDataNotInstalledForTheme($theme);
    }

    /**
     * Disables all themes
     */
    public function disableAll()
    {
        foreach ($this->all() as $theme) {
            \Craft::$app->plugins->disablePlugin($theme->handle);
        }
    }

    /**
     * Reset themes internal cache
     */
    public function resetThemes()
    {
        $this->themes = null;
    }

    /**
     * Loads all themes from the list of plugins
     */
    protected function loadThemes()
    {
        $this->themes = [];
        $plugins = \Craft::$app->plugins->getAllPlugins();
        foreach ($plugins as $plugin) {
            if ($plugin instanceof ThemeInterface) {
                $this->themes[$plugin->getHandle()] = $plugin;
            }
        }
    }
}