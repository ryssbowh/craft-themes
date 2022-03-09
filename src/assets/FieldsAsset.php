<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\View;

class FieldsAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    /**
     * @inheritDoc
     */
    public $jsOptions = [
        // 'position' => View::POS_HEAD
    ];

    public $js = [
        'fields.js'
    ];

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