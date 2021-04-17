<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use verbb\base\assetbundles\CpAsset;

class BlocksAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    public $js = [
        'chunk-vendors.js',
        'blocks.js'
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