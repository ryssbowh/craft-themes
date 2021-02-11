<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use craft\base\Component;
use yii\base\Event;

class Service extends Component
{
    protected function blockService(): BlockService
    {
        return Themes::$plugin->blocks;
    }

    protected function layoutService(): LayoutService
    {
        return Themes::$plugin->layouts;
    }

    protected function providerService(): BlockProvidersService
    {
        return Themes::$plugin->blockProviders;
    }

    protected function triggerEvent(string $type, Event $event) 
    {
        if ($this->hasEventHandlers($type)) {
            $this->trigger($type, $event);
        }
    }
}