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

    /**
     * Get name
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Get handle
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Displayer setter
     * 
     * @param FieldDisplayerInterface $displayer
     */
    public function setDisplayer(FieldDisplayerInterface $displayer);

    /**
     * @return FieldDisplayerInterface
     */
    public function getDisplayer(): FieldDisplayerInterface;
}