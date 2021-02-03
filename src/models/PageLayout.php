<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\records\LayoutLineRecord;
use craft\base\Model;

class PageLayout extends Model
{
	public $regions;
	public $theme;

	public function loadFromDb(): PageLayout
	{
		$this->regions = [];
		foreach ($this->theme->getRegions() as $region) {
			$this->regions[$region['handle']] = [];
		}
		$lines = LayoutLineRecord::find()->where(['theme' => $this->theme->getHandle()])->orderBy('order')->all();
		foreach ($lines as $line) {
			$this->regions[$line->region][] = $line->toBlock();
		}
		return $this;
	}
}