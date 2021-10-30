<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ElementsAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';

    public $css = [
        'element.css'
    ];

    public $depends = [
        CpAsset::class
    ];
}