<?php

namespace Ryssbowh\CraftThemes\models\blockCacheOptions;

use Ryssbowh\CraftThemes\models\BlockCacheStrategyOptions;

class GlobalOptions extends BlockCacheStrategyOptions
{
    /**
     * @var bool
     */
    public $cachePerAuthenticated = false;

    /**
     * @var bool
     */
    public $cachePerUser = false;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['cachePerAuthenticated', 'cachePerUser'], 'boolean'],
        ];
    }
}
