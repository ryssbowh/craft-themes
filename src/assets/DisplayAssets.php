<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class DisplayAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    public $js = [
        'chunk-vendors.js',
        'display.js',
    ];

    public $depends = [
        CpAsset::class,
        JquerySerializeJSON::class,
        FieldsAsset::class,
        FieldDisplayerAsset::class,
        FileDisplayerAsset::class
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