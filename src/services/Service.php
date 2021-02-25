<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use craft\base\Component;
use yii\base\Event;

class Service extends Component
{
    /**
     * @return BlockService
     */
    protected function blockService(): BlockService
    {
        return Themes::$plugin->blocks;
    }

    /**
     * @return ThemesRegistry
     */
    protected function themesRegistry(): ThemesRegistry
    {
        return Themes::$plugin->registry;
    }

    /**
     * @return ViewModeService
     */
    protected function viewModeService(): ViewModeService
    {
        return Themes::$plugin->viewModes;
    }

    /**
     * @return LayoutService
     */
    protected function layoutService(): LayoutService
    {
        return Themes::$plugin->layouts;
    }

    /**
     * @return BlockProvidersService
     */
    protected function providerService(): BlockProvidersService
    {
        return Themes::$plugin->blockProviders;
    }

    /**
     * Trigger an event
     * 
     * @param string $type
     * @param Event  $event
     */
    protected function triggerEvent(string $type, Event $event) 
    {
        if ($this->hasEventHandlers($type)) {
            $this->trigger($type, $event);
        }
    }
}