<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockOptionsInterface;
use craft\base\Model;

/**
 * Default class for block options
 */
class BlockOptions extends Model implements BlockOptionsInterface
{
    /**
     * @var string
     */
    public $cacheStrategy;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['cacheStrategy', 'string']
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function afterSave(BlockInterface $block)
    {
    }
}