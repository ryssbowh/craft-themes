<?php

namespace Ryssbowh\CraftThemes\helpers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\jobs\InstallThemeJob;
use Ryssbowh\CraftThemes\jobs\InstallThemesDataJob;
use Ryssbowh\CraftThemes\jobs\ReinstallLayoutsJob;
use Ryssbowh\CraftThemes\jobs\UninstallThemesDataJob;
use craft\base\Plugin;
use craft\base\PluginInterface;
use craft\helpers\Queue;

/**
 * Helper for operations related to plugins
 *
 * @since 3.1.0
 */
class PluginsHelper
{
    /**
     * This is needed to know where we should look for project config, either in yaml or in internal config.
     * In a test environment, project config is not written to yaml files so we need to force looking into internal one.
     * @var boolean
     */
    public static bool $readFromYaml = true;

    /**
     * Has a reinstall job been queued
     * @var boolean
     */
    private static bool $reinstallQueued = false;

    /**
     * Copy of the config plugins.themes.themesInstalled to track it internally
     * @var array
     */
    private static $installed;

    /**
     * When themes edition is changed
     *
     * @param  array  $oldEdition
     * @param  array  $newData
     */
    public static function onThemesEditionChanged(string $oldEdition, string $newEdition)
    {
        if (\Craft::$app->projectConfig->isApplyingExternalChanges) {
            return;
        }
        if ($oldEdition != $newEdition) {
            if (static::$installed === null) {
                static::$installed = \Craft::$app->projectConfig->get('plugins.themes.themesInstalled', static::$readFromYaml) ?? [];
            }
            if ($newEdition == Themes::EDITION_PRO) {
                //Change the edition on the plugin, it's not done at this point as the plugin service does it after the project config change
                Themes::$plugin->edition = $newEdition;
                foreach (Themes::$plugin->registry->all as $theme) {
                    static::installTheme($theme);
                }
            } else {
                foreach (Themes::$plugin->registry->all as $theme) {
                    static::uninstallTheme($theme);
                }
            }
        }
    }

    /**
     * Before a plugin is installed
     *
     * @param  PluginInterface $plugin
     */
    public static function beforeInstall(PluginInterface $plugin)
    {
        $config = \Craft::$app->projectConfig->get('plugins.' . $plugin->handle, static::$readFromYaml);
        if ($config) {
            //Already done in config, we must be applying config changes, abort
            return;
        }
        if (static::$installed === null) {
            static::$installed = \Craft::$app->projectConfig->get('plugins.themes.themesInstalled', static::$readFromYaml) ?? [];
        }
        if ($plugin instanceof ThemeInterface) {
            $extends = $plugin->extends;
            if ($extends) {
                \Craft::$app->plugins->installPlugin($extends);
            }
        } elseif (Themes::$plugin->isPluginRelated($plugin->handle)) {
            static::reinstallLayouts();
        }
    }

    /**
     * After a plugin is installed
     *
     * @param  PluginInterface $plugin
     */
    public static function afterInstall(PluginInterface $plugin)
    {
        if ($plugin instanceof ThemeInterface) {
            static::installTheme($plugin);
        }
    }

    /**
     * Before a plugin is uninstalled
     *
     * @param  PluginInterface $plugin
     */
    public static function beforeUninstall(PluginInterface $plugin)
    {
        $config = \Craft::$app->projectConfig->get('plugins.' . $plugin->handle, static::$readFromYaml);
        if (!$config) {
            //Already done in config, we must be applying config changes, abort
            return;
        }
        if (static::$installed === null) {
            static::$installed = \Craft::$app->projectConfig->get('plugins.themes.themesInstalled', static::$readFromYaml) ?? [];
        }
        if ($plugin instanceof ThemeInterface) {
            foreach (Themes::$plugin->registry->getDependencies($plugin) as $theme) {
                \Craft::$app->plugins->uninstallPlugin($theme->handle);
            }
            static::uninstallTheme($plugin);
            Themes::$plugin->rules->flushCache();
        } elseif ($plugin->handle == 'themes') {
            foreach (Themes::$plugin->registry->getAll() as $theme) {
                \Craft::$app->plugins->uninstallPlugin($theme->handle);
            }
        } elseif (Themes::$plugin->isPluginRelated($plugin->handle)) {
            static::reinstallLayouts();
        }
    }

    /**
     * Before a plugin is disabled
     *
     * @param PluginInterface $plugin
     */
    public static function beforeDisable(PluginInterface $plugin)
    {
        if ($plugin->handle == 'themes') {
            foreach (Themes::$plugin->registry->getAll() as $theme) {
                \Craft::$app->plugins->disablePlugin($theme->handle);
            }
        } elseif ($plugin instanceof ThemeInterface) {
            $deps = Themes::$plugin->registry->getDependencies($plugin);
            foreach ($deps as $theme) {
                \Craft::$app->plugins->disablePlugin($theme->handle);
            }
            Themes::$plugin->rules->flushCache();
        } elseif (Themes::$plugin->isPluginRelated($plugin->handle)) {
            static::reinstallLayouts();
        }
    }

    /**
     * After a plugin is disabled
     *
     * @param PluginInterface $plugin
     */
    public static function afterDisable(PluginInterface $plugin)
    {
        if ($plugin instanceof ThemeInterface) {
            Themes::$plugin->registry->resetThemes();
        }
    }

    /**
     * Before a plugin is enabled
     *
     * @param PluginInterface $plugin
     */
    public static function beforeEnable(PluginInterface $plugin)
    {
        if ($plugin instanceof ThemeInterface) {
            $extends = $plugin->extends;
            if ($extends) {
                \Craft::$app->plugins->enablePlugin($extends);
            }
            static::reinstallLayouts();
        } elseif (Themes::$plugin->isPluginRelated($plugin->handle)) {
            static::reinstallLayouts();
        }
    }

    /**
     * After a plugin is enabled
     *
     * @param PluginInterface $plugin
     */
    public static function afterEnable(PluginInterface $plugin)
    {
        if ($plugin instanceof ThemeInterface) {
            Themes::$plugin->registry->resetThemes();
        }
    }

    /**
     * Reinstall all layouts through a job
     */
    private static function reinstallLayouts()
    {
        if (Themes::$plugin->is(Themes::EDITION_PRO) and !static::$reinstallQueued) {
            Queue::push(new ReinstallLayoutsJob());
            static::$reinstallQueued = true;
        }
    }

    /**
     * Install a theme's data
     *
     * @param ThemeInterface $theme
     */
    private static function installTheme(ThemeInterface $theme)
    {
        Themes::$plugin->registry->resetThemes();
        if (static::$installed === null) {
            static::$installed = [];
        }
        $isInstalled = in_array($theme->handle, static::$installed);
        if (Themes::$plugin->is(Themes::EDITION_PRO) and !$isInstalled) {
            Themes::$plugin->layouts->installForTheme($theme);
            $theme->afterThemeInstall();
            static::$installed[] = $theme->handle;
            \Craft::$app->projectConfig->set('plugins.themes.themesInstalled', static::$installed, null, false);
        }
    }

    /**
     * Uninstall a theme's data
     *
     * @param ThemeInterface $theme
     */
    private static function uninstallTheme(ThemeInterface $theme)
    {
        Themes::$plugin->registry->resetThemes();
        if (static::$installed === null) {
            static::$installed = [];
        }
        $isInstalled = in_array($theme->handle, static::$installed);
        if (Themes::$plugin->is(Themes::EDITION_PRO) and $isInstalled) {
            Themes::$plugin->layouts->uninstallForTheme($theme);
            $theme->afterThemeUninstall();
            static::$installed = array_filter(static::$installed, function ($handle) use ($theme) {
                return $handle != $theme->handle;
            });
            \Craft::$app->projectConfig->set('plugins.themes.themesInstalled', static::$installed, null, false);
        }
    }
}
