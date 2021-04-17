<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\services\FieldsService;
use craft\base\Component;
use yii\base\Event;

class Service extends Component
{
    /**
     * @return BlockService
     */
    protected function blocksService(): BlockService
    {
        return Themes::$plugin->blocks;
    }

    /**
     * @return BlockService
     */
    protected function fieldDisplayersService(): FieldDisplayerService
    {
        return Themes::$plugin->fieldDisplayers;
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
    protected function viewModesService(): ViewModeService
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
    protected function blockProviderService(): BlockProvidersService
    {
        return Themes::$plugin->blockProviders;
    }

    /**
     * @return CacheService
     */
    protected function cacheService(): CacheService
    {
        return Themes::$plugin->cache;
    }

    /**
     * @return DisplayService
     */
    protected function displayService(): DisplayService
    {
        return Themes::$plugin->display;
    }

    /**
     * @return DisplayService
     */
    protected function groupsService(): GroupsService
    {
        return Themes::$plugin->groups;
    }

    /**
     * @return FieldsService
     */
    protected function fieldsService(): FieldsService
    {
        return Themes::$plugin->fields;
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