<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;
use craft\base\Model;
use craft\elements\Asset;

/**
 * A file displayer renders an asset. It can handle several file kinds
 */
interface FileDisplayerInterface extends DisplayerInterface
{
    /**
     * Prefix used when caching this type of displayers
     */
    const CACHE_PREFIX = 'file';

    /**
     * Which file kind this displayer can handle.
     * Developers should use FileDisplayerService::getKindTargets(string $displayerHandle) instead
     * 
     * '*' is a valid kind and will be resolved to all asset kinds
     * 
     * @return string[]
     */
    public static function getKindTargets(): array;

    /**
     * Is this displayer the default for an asset kind
     * 
     * @param  string  $kind
     * @return boolean
     */
    public static function isDefault(string $kind): bool;

    /**
     * Get options
     * 
     * @return Model
     */
    public function getOptions(): FileDisplayerOptions;

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
     * @return bool
     */
    public function beforeRender(Asset $asset): bool;
}