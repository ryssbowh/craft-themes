<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\base\Model;

/**
 * A displayer renders one and one only type of field, it can have options.
 */
interface FieldDisplayerInterface extends DisplayerInterface
{
    /**
     * Prefix used when caching this type of displayers
     */
    const CACHE_PREFIX = 'field';
    
    /**
     * Field classes this displayer can handle.
     * Developers should use FieldDisplayerService::getFieldTargets($displayerHandle) instead.
     * 
     * @return array
     */
    public static function getFieldTargets(): array;

    /**
     * Is this displayer the default for a field class
     * 
     * @param  string  $fieldClass
     * @return boolean
     */
    public static function isDefault(string $fieldClass): bool;

    /**
     * Field setter
     * 
     * @param FieldInterface $field
     */
    public function setField(FieldInterface $field);

    /**
     * Get options
     * 
     * @return FieldDisplayerOptions
     */
    public function getOptions(): FieldDisplayerOptions;

    /**
     * Options setter
     * 
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * Theme getter
     * 
     * @return ThemeInterface
     */
    public function getTheme(): ThemeInterface;

    /**
     * Callback before rendering, returning false will skip the field rendering.
     * The value will be null when this is called for a cached content block.
     * 
     * @param  &$value
     * @return bool
     */
    public function beforeRender(&$value): bool;
}