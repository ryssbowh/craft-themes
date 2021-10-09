<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\services\FieldsService;
use craft\base\Component;
use yii\base\Event;

abstract class Service extends Component
{
    /**
     * Get the blocks service
     * 
     * @return BlockService
     */
    protected function blocksService(): BlockService
    {
        return Themes::$plugin->blocks;
    }

    /**
     * Get the field displayers service
     * 
     * @return FieldDisplayerService
     */
    protected function fieldDisplayersService(): FieldDisplayerService
    {
        return Themes::$plugin->fieldDisplayers;
    }

    /**
     * Get the themes registry
     * 
     * @return ThemesRegistry
     */
    protected function themesRegistry(): ThemesRegistry
    {
        return Themes::$plugin->registry;
    }

    /**
     * Get the view mode service
     * 
     * @return ViewModeService
     */
    protected function viewModesService(): ViewModeService
    {
        return Themes::$plugin->viewModes;
    }

    /**
     * Get the layout service
     * 
     * @return LayoutService
     */
    protected function layoutService(): LayoutService
    {
        return Themes::$plugin->layouts;
    }

    /**
     * Get the block providers service
     * 
     * @return BlockProvidersService
     */
    protected function blockProviderService(): BlockProvidersService
    {
        return Themes::$plugin->blockProviders;
    }

    /**
     * Get the block cache service
     * 
     * @return BlockCacheService
     */
    protected function blockCacheService(): BlockCacheService
    {
        return Themes::$plugin->blockCache;
    }

    /**
     * Get the display service
     * 
     * @return DisplayService
     */
    protected function displayService(): DisplayService
    {
        return Themes::$plugin->displays;
    }

    /**
     * Get the groups service
     * 
     * @return GroupsService
     */
    protected function groupsService(): GroupsService
    {
        return Themes::$plugin->groups;
    }

    /**
     * Get the fields service
     * 
     * @return FieldsService
     */
    protected function fieldsService(): FieldsService
    {
        return Themes::$plugin->fields;
    }

    /**
     * Get the matrix service
     * 
     * @return MatrixService
     */
    protected function matrixService(): MatrixService
    {
        return Themes::$plugin->matrix;
    }

    /**
     * Get the file displayers service
     * 
     * @return FileDisplayerService
     */
    protected function fileDisplayerService(): FileDisplayerService
    {
        return Themes::$plugin->fileDisplayers;
    }

    /**
     * Triggers an event
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