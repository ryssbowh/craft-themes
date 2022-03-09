<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;

class BlocksAssets extends AssetBundle
{
    /**
     * @inheritDoc
     */
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    /**
     * @inheritDoc
     */
    public $js = [
        'blocks.js',
    ];

    /**
     * @inheritDoc
     */
    public $depends = [
        VueAssets::class
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