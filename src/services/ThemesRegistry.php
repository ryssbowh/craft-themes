<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Theme;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\ThemeEvent;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\base\PluginInterface;
use craft\events\RegisterTemplateRootsEvent;
use craft\models\Site;


class ThemesRegistry extends Service
{   
    const EVENT_THEME_SET = 'themes.set';
    const THEMES_WEBROOT = '@webroot/themes/';
    const EVENT_AFTER_INSTALL_THEME = 'after_install_theme';
    const EVENT_AFTER_UNINSTALL_THEME = 'after_uninstall_theme';

    /**
     * @var ?array
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
        if (\Craft::$app->request->getIsSiteRequest()) {
            $path = \Craft::$app->request->getPathInfo();
            $path = $path === '' ? '/' : $path;
            $this->currentTheme->registerAssetBundles($path);
        }
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
    }

    /**
     * Get all themes
     * 
     * @return array
     */
    public function all(): array
    {
        if ($this->themes === null) {
            $this->loadThemes();
        }
        return $this->themes;
    }

    /**
     * Get all themes as names
     * 
     * @return array
     */
    public function getAsNames(): array
    {
        return array_map(function ($theme) {
            return $theme->name;
        }, $this->all());
    }

    /**
     * Get all non partial themes
     * 
     * @param  boolean $asNames
     * @param  boolean $asArrays
     * @return array
     */
    public function getNonPartials(bool $asNames = false, bool $asArrays = false): array
    {
        $themes = array_filter($this->all(), function ($theme) {
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
     * @return array
     */
    public function getDependencies(ThemeInterface $theme): array
    {
        return array_filter($this->all(), function ($theme2) use ($theme) {
            return $theme2->extends == $theme->handle;
        });
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
        if (isset($this->all()[$handle])) {
            return $this->all()[$handle];
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
        return isset($this->all()[$handle]);
    }

    /**
     * Install a theme
     *
     * @return ThemeInterface $theme
     */
    public function installTheme(ThemeInterface $theme)
    {
        $this->resetThemes();
        if (Themes::$plugin->is(Themes::EDITION_PRO)) {
            if (Themes::$plugin->layouts->installThemeData($theme)) {
                $theme->afterThemeInstall();
            }
        }
        $this->triggerEvent(self::EVENT_AFTER_INSTALL_THEME, new ThemeEvent([
            'theme' => $theme
        ]));
    }

    /**
     * Uninstall a theme
     *
     * @return ThemeInterface $theme
     */
    public function uninstallTheme(ThemeInterface $theme)
    {
        Themes::$plugin->rules->flushCache();
        if (Themes::$plugin->is(Themes::EDITION_PRO)) {
            if (Themes::$plugin->layouts->uninstallThemeData($theme)) {
                $theme->afterThemeUninstall();
            }
        }
        $this->triggerEvent(self::EVENT_AFTER_UNINSTALL_THEME, new ThemeEvent([
            'theme' => $theme
        ]));
        $this->resetThemes();
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
        $themes = [];
        $plugins = \Craft::$app->plugins->getAllPlugins();
        foreach ($plugins as $plugin) {
            if ($plugin instanceof ThemeInterface) {
                $themes[$plugin->getHandle()] = $plugin;
            }
        }
        $this->themes = $themes;
    }
}