<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\records\BlockRecord;
use craft\base\Model;

class Layout extends Model
{
	public $blocks = [];
	public $theme;

	public function init()
	{
		parent::init();
		if ($this->theme === null) {
			throw LayoutException::noTheme();
		}
		if (is_string($this->theme)) {
			$this->theme = Themes::$plugin->registry->getTheme($this->theme);
		}
	}

	public function buildRegionsFromRawData(array $data)
	{
		foreach ($data as $handle => $region) {
			if (!isset($this->regions[$handle])) {
				continue;
			}
			foreach ($region['blocks'] as $blockData) {
				if ($blockData['id'] ?? false) {
					$block = Themes::$plugin->blocks->getById($blockData['id']);
				} else {
					$provider = Themes::$plugin->blockProviders->getByHandle($blockData['provider']);
					$block = $provider->getBlock($blockData['handle'], $blockData);
				}
				unset($blockData['handle']);
				$block->setAttributes($blockData);
				$this->regions[$handle]->blocks[] = $block;
			}
		}
	}

	public function loadFromDb(): Layout
	{
		$this->blocks = Themes::$plugin->blocks->getForTheme($this->theme);
		return $this;
	}
}