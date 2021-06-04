<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Model;

interface FileDisplayerInterface
{
    /**
     * File kind targets.
     * 
     * array[string] or
     * '*' for all asset kinds
     * 
     * @return array[string]|string
     */
    public static function getKindTargets();

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
}