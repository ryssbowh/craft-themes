<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class JquerySerializeJSON extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';

    public $js = [
        'jquery.serializejson.js'
    ];

    public $depends = [
        CpAsset::class
    ];
}