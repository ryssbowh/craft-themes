<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SettingsAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';

    public $js = [
        'settings.js'
    ];

    public $css = [
        'settings.css'
    ];

    public $depends = [
        CpAsset::class
    ];
}