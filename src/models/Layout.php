<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\records\LayoutLineRecord;
use craft\base\Model;

class Layout extends Model
{
	public $regions;
	public $theme;

	public function buildRegionsFromRawData(array $data)
	{
		foreach ($data as $name => $lines) {
			foreach ($lines as $line) {
				$line['theme'] = $this->theme->getHandle();
				$this->regions[$name][] = new LayoutLine($line);
			}
		}
	}

	public function loadFromDb(): Layout
	{
		$this->regions = [];
		$lines = LayoutLineRecord::find()->where(['theme' => $this->theme->getHandle()])->orderBy('order')->all();
		foreach ($lines as $line) {
			$this->regions[$line->region][] = $line->toModel();
		}
		return $this;
	}
}