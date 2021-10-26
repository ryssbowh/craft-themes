<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\assets\cp\CpAsset;

class DisplayAssets extends ThemesBaseAssets
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    public $js = [
        'chunk-vendors.js',
        'display.js',
    ];

    public $depends = [
        CpAsset::class,
        FieldsAsset::class,
        FieldDisplayerAsset::class,
        FileDisplayerAsset::class
    ];
}