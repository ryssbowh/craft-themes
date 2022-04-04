<?php
namespace Ryssbowh\CraftThemes\base;

use Ryssbowh\CraftThemes\events\RegisterBlockProviderBlocks;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use craft\base\Component;

/**
 * Base class for all block providers
 */
abstract class BlockProvider extends Component implements BlockProviderInterface
{
    /**
     * block classes defined by this provider
     * @var string[]
     */
    protected $_definedBlocks = [];

    /**
     * block classes defined by this provider, indexed by blocks handles
     * @var string[]
     */
    protected $_allDefinedBlocks;

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

    /**
     * @inheritDoc
     */
    public function getDefinedBlocks(): array
    {
        if ($this->_allDefinedBlocks === null) {
            $event = new RegisterBlockProviderBlocks([
                'blocks' => $this->_definedBlocks,
                'provider' => $this
            ]);
            if ($this->hasEventHandlers(BlockProviderInterface::EVENT_REGISTER_BLOCKS)) {
                $this->trigger(BlockProviderInterface::EVENT_REGISTER_BLOCKS, $event);    
            }
            $blocks = [];
            foreach ($event->blocks as $class) {
                if (!preg_match('/^[a-zA-Z0-9\-]+$/', $class::$handle)) {
                    throw BlockProviderException::handleInvalid($class);   
                }
                if (isset($blocks[$class::$handle])) {
                    throw BlockProviderException::blockDefined($class::$handle, $this->handle);
                }
                $blocks[$class::$handle] = $class;
            }
            $this->_allDefinedBlocks = $blocks;
        }
        return $this->_allDefinedBlocks;
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return array_merge(parent::fields(), ['blocks', 'name', 'handle']);
    }
}