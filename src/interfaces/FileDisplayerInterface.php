<?php
namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Model;
use craft\elements\Asset;

/**
 * A file displayer renders an asset. It can handle several file kinds
 */
interface FileDisplayerInterface
{
    /**
     * Which file kind this displayer can handle.
     * 
     * array[string] or
     * '*' for all asset kinds
     * 
     * @return array|string
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
     * Displayer getter
     * 
     * @return FieldDisplayerInterface
     */
    public function getDisplayer(): FieldDisplayerInterface;

    /**
     * Callback before rendering, returning false will skip the file rendering
     *
     * @param  Asset $asset
     * @return bool;
     */
    public function beforeRender(Asset $asset): bool;
}