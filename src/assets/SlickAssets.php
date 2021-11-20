<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use yii\web\JqueryAsset;

class SlickAssets extends AssetBundle
{
    /**
     * @inheritDoc
     */
    public $sourcePath = __DIR__ . '/dist';

    /**
     * @inheritDoc
     */
    public $js = [
        'slick.js'
    ];

    public $depends = [
        SlickLibAssets::class
    ];
}