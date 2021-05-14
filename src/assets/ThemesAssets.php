<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ThemesAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';

    public $css = [
        'themes.css'
    ];

    public $depends = [
        CpAsset::class
    ];
}