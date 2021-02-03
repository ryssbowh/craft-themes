<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use craft\base\Component;

class BlockService extends Component
{
	public static function createBlock($block): BlockInterface
	{
		if (is_array($block)) {
			if (!isset($block['class'])) {
				throw BlockException::noClass(__METHOD__);
			}
			$blockClass = $block['class'];
			unset($block['class']);
			$block = new $blockClass($block);
		} elseif (is_string($block)) {
			$block = new $block;
		}
		if (!$block instanceof BlockInterface) {
			throw BlockException::notAblock(__METHOD__);
		}
		return $block;
	}
}