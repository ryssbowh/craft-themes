<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use yii\web\JqueryAsset;

class SlickLibAssets extends AssetBundle
{
    /**
     * @inheritDoc
     */
    public $sourcePath = __DIR__ . '/lib/slick-1.8.1';

    /**
     * @inheritDoc
     */
    public $js = [
        'slick.min.js'
    ];

    public $css = [
        'slick.css',
        'slick-theme.css'
    ];

    public $depends = [
        JqueryAsset::class
    ];
}