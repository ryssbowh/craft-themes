<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\assets\cp\CpAsset;

/**
 * @since 3.3.0
 */
class UtilitiesAssets extends ThemesBaseAssets
{
    public $sourcePath = __DIR__ . '/dist';

    public $js = [
        'utilities.js'
    ];

    public $css = [
        'utilities.css'
    ];

    public $depends = [
        CpAsset::class
    ];
}