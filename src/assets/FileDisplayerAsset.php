<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;

class FileDisplayerAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    public $js = [
        'fileDisplayers.js'
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