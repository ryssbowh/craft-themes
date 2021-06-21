<?php 

namespace Ryssbowh\CraftThemes\models;

use craft\base\Model;

class BlockCacheStrategyOptions extends Model
{
    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return $this->attributes;
    }
}