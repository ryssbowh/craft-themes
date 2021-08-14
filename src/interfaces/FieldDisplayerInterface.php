<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Model;

interface FieldDisplayerInterface
{
    /**
     * Field target class
     * 
     * @return string
     */
    public static function getFieldTarget(): string;

    /**
     * Get handle
     * 
     * @return string
     */
    public static function getHandle(): string;

    /**
     * Get options model class
     * 
     * @return string
     */
    public function getOptionsModel(): string;

    /**
     * Get options
     * 
     * @return Model
     */
    public function getOptions(): Model;

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
}