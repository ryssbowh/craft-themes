<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\models\LayoutLine;
use craft\db\ActiveRecord;

class BlockRecord extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%themes_blocks}}';
	}

	public function getProvider(): BlockProviderInterface
	{
		return Themes::$plugin->blockProviders->getByHandle($this->provider);
	}

	public function toModel(): BlockInterface
	{
		return $this->getProvider()->getBlock($this->handle, $this->getAttributes());
	}
}