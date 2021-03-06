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
    public function getFieldTarget(): string;

    /**
     * Is this displayer the default for the field it displays
     * 
     * @return bool
     */
    public function isDefault(): bool;

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

    /**
     * @return string
     */
    public function getOptionsHtml(): string;
}