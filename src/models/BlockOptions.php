<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use craft\base\Model;

class BlockOptions extends Model
{
    public $caching = 0;

    public function defineRules(): array
    {
        return [
            ['caching', 'required']
        ];
    }

    public function getConfig(): array
    {
        return $this->attributes;
    }

    public function afterSave(BlockInterface $block)
    {
    }
}