<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use verbb\base\assetbundles\CpAsset;

class BlocksAssets extends AssetBundle
{
	public $sourcePath = __DIR__ . '/../../vue/dist/js';

	public $js = [
		'chunk-vendors.js',
		'app.js'
	];

	public $depends = [
		CpAsset::class
	];

	public function init()
    {
    	parent::init();
    	$this->sourcePath = realpath($this->sourcePath);
    }
}