<?php
namespace Ryssbowh\CraftThemes;

use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use craft\events\PluginEvent;
use craft\services\Plugins;
use yii\base\Event;
use yii\base\Module;

class Installer extends Module
{
    public function init()
    {
        parent::init();

        // Make sur the themes plugin is installed first and install its dependencies before a theme is installed
        Event::on(Plugins::class, Plugins::EVENT_BEFORE_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    $extends = $event->plugin->extends;
                    \Craft::$app->plugins->installPlugin('themes');
                    if ($extends) {
                        \Craft::$app->plugins->installPlugin($extends);
                    }
                }
            }
        );

        // Install theme's data after a theme is installed
        Event::on(Plugins::class, Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    Themes::$plugin->registry->installTheme($event->plugin);
                }
            }
        );

        // Uninstall all themes dependency and data before a theme is uninstalled
        Event::on(Plugins::class, Plugins::EVENT_BEFORE_UNINSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    $deps = Themes::$plugin->registry->getDependencies($event->plugin);
                    foreach ($deps as $theme) {
                        \Craft::$app->plugins->uninstallPlugin($theme->handle);
                    }
                    Themes::$plugin->registry->uninstallTheme($event->plugin);
                }
            }
        );
    }
}