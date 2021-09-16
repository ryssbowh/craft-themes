<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class BlocksAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    public $js = [
        'blockOptions.js',
        'blockStrategies.js',
        'chunk-vendors.js',
        'blocks.js',
    ];

    public $depends = [
        CpAsset::class,
        JquerySerializeJSON::class
    ];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->sourcePath = realpath($this->sourcePath);
    }
}