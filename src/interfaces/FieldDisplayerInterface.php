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
     * Get options model
     * 
     * @return Model
     */
    public function getOptionsModel(): ?Model;

    /**
     * Get options
     * 
     * @return Model
     */
    public function getOptions(): Model;

    public function getName(): string;

    public function eagerLoad(): array;
}