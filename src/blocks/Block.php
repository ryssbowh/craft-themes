<?php 

namespace Ryssbowh\CraftThemes\blocks;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use craft\base\Model;

abstract class Block extends Model implements BlockInterface
{
	protected $handle = '';

	protected $name = '';

	public function getName(): string
	{
		return $this->name;
	}

	public function getHandle(): string
	{
		return $this->handle;
	}
}