<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\View;
use craft\web\assets\cp\CpAsset;
use craft\web\assets\timepicker\TimepickerAsset;

class VueAssets extends ThemesBaseAssets
{
    /**
     * @inheritDoc
     */
    public $sourcePath = __DIR__ . '/../../vue/dist/js';

    /**
     * @inheritDoc
     */
    public $jsOptions = [
        // 'position' => View::POS_HEAD
    ];

    /**
     * @inheritDoc
     */
    public $js = [
        'chunk-vendors.js',
        'vue.js'
    ];

    /**
     * @inheritDoc
     */
    public $depends = [
        CpAsset::class,
        TimepickerAsset::class
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