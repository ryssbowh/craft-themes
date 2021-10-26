<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\assets\cp\CpAsset;

class BlocksAssets extends ThemesBaseAssets
{
    /**
     * @inheritDoc
     */
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    /**
     * @inheritDoc
     */
    public $js = [
        'blockOptions.js',
        'blockStrategies.js',
        'chunk-vendors.js',
        'blocks.js',
    ];

    /**
     * @inheritDoc
     */
    public $depends = [
        CpAsset::class
    ];
}