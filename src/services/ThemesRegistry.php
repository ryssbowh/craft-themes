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
    const THEME_SET_EVENT = 'themes.set';

    /**
     * @var ?array
     */
    protected $themes;

    /**
     * @var ?ThemeInterface
     */
    protected $currentTheme;

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
            $this->currentTheme = $this->getTheme($theme);
        } elseif ($theme instanceof ThemeInterface) {
            $this->currentTheme = $theme;
        } elseif ($theme === null) {
            $this->currentTheme = null;
        }
        if ($this->currentTheme) {
            \Yii::setAlias('@themePath', '@root/themes/' . $theme->handle);
            \Yii::setAlias('@themeWebPath', '@webroot/themes/' . $theme->handle);
            \Craft::$app->view->registerTwigExtension(new TwigTheme);
            $path = \Craft::$app->request->getPathInfo();
            $this->currentTheme->registerAssetBundles($path);
            $this->currentTheme->afterSet();
        }
        $this->triggerEvent(
            self::THEME_SET_EVENT, 
            new ThemeEvent(['theme' => $this->currentTheme])
        );
        if (!$this->currentTheme) {
            \Craft::info("No theme found for request ".\Craft::$app->request->getUrl(), __METHOD__);
        } else {
            \Craft::info("Theme has been set to : " . $this->currentTheme->name, __METHOD__);
        }
        return $this->currentTheme;
    }

    public function registerCurrentThemeTemplates(RegisterTemplateRootsEvent $event)
    {
        if (!$this->currentTheme) {
            return;
        }
        $event->roots[''][] = __DIR__ . '/../templates/front';
        $event->roots[''] = array_merge($this->currentTheme->getTemplatePaths(), $event->roots['']);#
    }

    /**
     * Get all themes
     * 
     * @return array
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
     * @return array
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
     * @return array
     */
    public function getNonPartials(bool $asNames = false, bool $asArray = false): array
    {
        $themes = array_filter($this->getAll(), function ($theme) {
            return !$theme->isPartial();
        });
        if ($asNames) {
            return array_map(function ($theme) {
                return $theme->name;
            }, $themes);
        } elseif ($asArray) {
            return array_map(function ($theme) {
                return $theme->toArray();
            }, $themes);
        }
        return $themes;
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