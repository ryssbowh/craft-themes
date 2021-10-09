<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class RulesAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';

    public $js = [
        'rules.js'
    ];

    public $depends = [
        CpAsset::class
    ];
}