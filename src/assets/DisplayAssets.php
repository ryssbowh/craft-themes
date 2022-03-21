<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;

class DisplayAssets extends AssetBundle
{
    /**
     * @inheritDoc
     */
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    /**
     * @inheritDoc
     */
    public $js = [
        'display.js'
    ];

    /**
     * @inheritDoc
     */
    public $depends = [
        FieldsAsset::class
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