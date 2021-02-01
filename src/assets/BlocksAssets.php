<?php 

namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use verbb\base\assetbundles\CpAsset;

class BlocksAssets extends AssetBundle
{
	public $sourcePath = __DIR__ . '/dist';

	public $js = [
		'blocks.js'
	];

	public $css = [
		'blocks.css'
	];

	public $depends = [
		CpAsset::class
	];
}