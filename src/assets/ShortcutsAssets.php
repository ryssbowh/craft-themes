<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ShortcutsAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';

    public $js = [
        'shortcuts.js'
    ];

    public $css = [
        'shortcuts.css'
    ];
}