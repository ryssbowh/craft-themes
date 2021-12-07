<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\base\Model;

/**
 * A displayer renders one and one only type of field, it can have options.
 */
interface FieldDisplayerInterface 
{
    /**
     * Field classes this displayer can handle.
     * Developers should use FieldDisplayerService::getFieldTargets($displayerHandle) instead.
     * 
     * @return array
     */
    public static function getFieldTargets(): array;

    /**
     * Get handle
     * 
     * @return string
     */
    public static function getHandle(): string;

    /**
     * Is this displayer the default for a field class
     * 
     * @param  string  $fieldClass
     * @return boolean
     */
    public static function isDefault(string $fieldClass): bool;

    /**
     * Description/helper shown in CP
     * 
     * @return string
     */
    public function getDescription(): string;

    /**
     * Field setter
     * 
     * @param FieldInterface $field
     */
    public function setField(FieldInterface $field);

    /**
     * Field getter
     * 
     * @return FieldInterface
     */
    public function getField(): FieldInterface;

    /**
     * Get options model class
     * 
     * @return string
     */
    public function getOptionsModel(): string;

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
     * Does this displayer define options
     * 
     * @return bool
     */
    public function getHasOptions(): bool;

    /**
     * Theme getter
     * 
     * @return ThemeInterface
     */
    public function getTheme(): ThemeInterface;

    /**
     * Get name
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Eager load fields
     * 
     * @return array
     */
    public function eagerLoad(): array;

    /**
     * Callback before rendering, returning false will skip the field rendering.
     * The value will be null when this is called for a cached content block.
     * 
     * @param  &$value
     * @return bool
     */
    public function beforeRender(&$value): bool;
}