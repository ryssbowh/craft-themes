<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\assets\cp\CpAsset;
use craft\web\assets\timepicker\TimepickerAsset;

class DisplayAssets extends ThemesBaseAssets
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    public $js = [
        'chunk-vendors.js',
        'chunk-common.js',
        'display.js',
    ];

    public $depends = [
        CpAsset::class,
        FieldsAsset::class,
        TimepickerAsset::class
    ];
}