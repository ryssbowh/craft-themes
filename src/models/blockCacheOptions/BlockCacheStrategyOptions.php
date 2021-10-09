<?php
namespace Ryssbowh\CraftThemes\models\blockCacheOptions;

use craft\base\Model;

/**
 * Base class for all block cache strategies options
 */
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