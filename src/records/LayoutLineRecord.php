<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\models\LayoutLine;
use craft\db\ActiveRecord;

class LayoutLineRecord extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%theme_block_layouts}}';
	}

	public function toModel(): LayoutLine
	{
		return new LayoutLine($this->getAttributes());
	}

	public function getProvider(): BlockProviderInterface
	{
		return Themes::$plugin->blockProviders->getByHandle($this->blockProvider);
	}

	public function toBlock(): BlockInterface
	{
		return $this->getProvider()->getBlock($this->blockHandle);
	}
}