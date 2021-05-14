<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use verbb\base\assetbundles\CpAsset;

class BlockOptionsAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    public $js = [
        'blockOptions.js'
    ];

    public function init()
    {
        parent::init();
        $this->sourcePath = realpath($this->sourcePath);
    }
}