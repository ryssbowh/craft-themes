<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockOptionsInterface;
use craft\base\Model;

class BlockOptions extends Model implements BlockOptionsInterface
{
    public $caching = 0;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['caching', 'required']
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