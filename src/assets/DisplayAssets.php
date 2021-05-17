<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use verbb\base\assetbundles\CpAsset;

class DisplayAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    public $js = [
        'chunk-vendors.js',
        'displayers.js',
        'display.js',
    ];

    public $depends = [
        CpAsset::class,
        JquerySerializeJSON::class
    ];

    public function init()
    {
        parent::init();
        $this->sourcePath = realpath($this->sourcePath);
    }
}