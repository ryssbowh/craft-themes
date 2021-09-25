<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ListAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';

    public $css = [
        'list.css'
    ];

    public $depends = [
        CpAsset::class
    ];
}