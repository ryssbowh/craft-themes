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
use craft\helpers\Queue;

/**
 * Helper for operations related to plugins
 *
 * @since 3.1.0
 */
class PluginsHelper
{
    /**
     * Callback when a plugin is added to project config
     * 
     * @param  string $handle
     * @param  array  $data
     */
    public static function onAdd(string $handle, array $data)
    {
        if (\Craft::$app->projectConfig->isApplyingYamlChanges) {
            return;
        }
        if ($data['enabled']) {
            $class = \Craft::$app->plugins->getComposerPluginInfo($handle)['class'];
            static::onPluginInstalled($handle, $class);   
        }
    }

    /**
     * Callback when a plugin is removed from project config
     * 
     * @param  string $handle
     * @param  array  $data
     */
    public static function onRemove(string $handle, array $data)
    {
        if (\Craft::$app->projectConfig->isApplyingYamlChanges) {
            return;
        }
        if ($handle == 'themes') {
            return static::onThemesUninstalled($handle);    
        }
        static::onPluginUninstalled($handle);
    }

    /**
     * Callback when a plugin is updated in project config
     * 
     * @param  string $handle
     * @param  array  $oldData
     * @param  array  $oldData
     */
    public static function onUpdate(string $handle, array $oldData, array $newData)
    {
        if (\Craft::$app->projectConfig->isApplyingYamlChanges) {
            return;
        }
        $disabled = ($oldData['enabled'] and !$newData['enabled']);
        $enabled = (!$oldData['enabled'] and $newData['enabled']);
        $schemaChanged = ($oldData['schemaVersion'] != $newData['schemaVersion']);
        $editionChanged = ($oldData['edition'] != $newData['edition']);
        if ($handle === 'themes') {
            if ($disabled) {
                return static::onThemesDisabled();
            }
            if ($editionChanged) {
                return static::onThemesEditionChanged($oldData['edition'], $newData['edition']);
            }
            if ($schemaChanged) {
                return static::onThemesSchemaVersionChanged();
            }
        } else {
            if ($enabled) {
                return static::onPluginEnabled($handle);
            }
            if ($disabled) {
                return static::onPluginDisabled($handle);
            }
        }
    }

    /**
     * When a plugin is enabled
     * 
     * @param  string $handle
     */
    protected static function onPluginEnabled(string $handle)
    {
        $plugin = \Craft::$app->plugins->getPlugin($handle);
        if ($event->plugin instanceof ThemeInterface) {
            $extends = $event->plugin->extends;
            if ($extends) {
                \Craft::$app->plugins->enablePlugin($extends);
            }
            Themes::$plugin->registry->resetThemes();
        }
        if (Themes::$plugin->is(Themes::EDITION_PRO) and Themes::$plugin->isPluginRelated($handle)) {
            Queue::push(new ReinstallLayoutsJob);
        }
    }

    /**
     * When a plugin is disabled
     * 
     * @param string $handle
     */
    protected static function onPluginDisabled(string $handle)
    {
        $plugin = \Craft::$app->plugins->getPlugin($handle);
        if ($plugin instanceof ThemeInterface) {
            $deps = Themes::$plugin->registry->getDependencies($plugin);
            foreach ($deps as $theme) {
                \Craft::$app->plugins->disablePlugin($theme->handle);
            }
            Themes::$plugin->registry->resetThemes();
            Themes::$plugin->rules->flushCache();
        }
        if (Themes::$plugin->is(Themes::EDITION_PRO) and Themes::$plugin->isPluginRelated($handle)) {
            Queue::push(new ReinstallLayoutsJob);
        }
    }

    /**
     * When a plugin is installed
     * 
     * @param string $handle
     * @param string $class
     */
    protected static function onPluginInstalled(string $handle, string $class)
    {
        if (!Themes::$plugin->is(Themes::EDITION_PRO) or
            is_subclass_of($class, ThemeInterface::class) or
            !Themes::$plugin->isPluginRelated($handle)
        ) {
            return;
        }
        Queue::push(new ReinstallLayoutsJob);
    }

    /**
     * When a plugin is uninstalled
     * 
     * @param string $handle
     */
    protected static function onPluginUninstalled(string $handle)
    {
        $plugin = \Craft::$app->plugins->getPlugin($handle);
        if (!Themes::$plugin->is(Themes::EDITION_PRO) or 
            $plugin instanceof ThemeInterface or
            !Themes::$plugin->isPluginRelated($handle)
        ) {
            return;
        }
        Queue::push(new ReinstallLayoutsJob);
    }

    /**
     * When the themes plugin is uninstalled
     */
    protected static function onThemesUninstalled()
    {
        foreach (Themes::$plugin->registry->getAll() as $plugin) {
            \Craft::$app->plugins->uninstallPlugin($plugin->handle);
        }
    }

    /**
     * When the themes plugin is disabled
     */
    protected static function onThemesDisabled()
    {
        foreach (Themes::$plugin->registry->getAll() as $theme) {
            \Craft::$app->plugins->disablePlugin($theme->handle);
        }
    }

    /**
     * When the themes plugin changes schema version
     */
    protected static function onThemesSchemaVersionChanged()
    {
        Queue::push(new ReinstallLayoutsJob);
    }

    /**
     * When the themes plugin changes edition
     */
    protected static function onThemesEditionChanged(string $oldEdition, string $newEdition)
    {
        if ($newEdition == Themes::EDITION_PRO) {
            Queue::push(new InstallThemesDataJob);
        } elseif ($newEdition == Themes::EDITION_LITE) {
            Queue::push(new UninstallThemesDataJob);
        }
    }
}