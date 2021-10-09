<?php
namespace Ryssbowh\CraftThemes\models\blockCacheOptions;

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
     * @var bool
     */
    public $cachePerViewport = false;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['cachePerAuthenticated', 'cachePerUser', 'cachePerViewport'], 'boolean', 'trueValue' => true, 'falseValue' => false],
        ];
    }
}
