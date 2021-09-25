<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\assets\cp\CpAsset;

class BlocksAssets extends ThemesBaseAssets
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    public $js = [
        'blockOptions.js',
        'blockStrategies.js',
        'chunk-vendors.js',
        'blocks.js',
    ];

    public $depends = [
        CpAsset::class,
        JquerySerializeJSON::class
    ];
}