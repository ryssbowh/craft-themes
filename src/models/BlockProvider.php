<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use craft\base\Model;

abstract class BlockProvider extends Model implements BlockProviderInterface
{
    protected $_definedBlocks = [];

    /**
     * @inheritDoc
     */
    public function getBlocks(): array
    {
        $_this = $this;
        $blocks = array_map(function ($handle) use ($_this) {
            return $_this->createBlock($handle);
        }, array_keys($this->definedBlocks));
        usort($blocks, function ($a, $b) {
            return ($a->name < $b->name) ? -1 : 1;
        });
        return $blocks;
    }

    /**
     * @inheritDoc
     */
    public function createBlock(string $handle): BlockInterface
    {
        $defined = $this->definedBlocks;
        if (isset($defined[$handle])) {
            $class = $defined[$handle];
            return new $class(['provider' => $this->handle]);
        }
        throw BlockProviderException::noBlock(get_called_class(), $handle);
    }

    public function getDefinedBlocks(): array
    {
        $blocks = [];
        foreach ($this->_definedBlocks as $class) {
            $blocks[$class::$handle] = $class;
        }
        return $blocks;
    }

    /**
     * @inheritDoc
     */
    public function addDefinedBlock(string $blockClass): BlockProviderInterface
    {
        $this->_definedBlocks[] = $block;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['blocks', 'name', 'handle']);
    }
}