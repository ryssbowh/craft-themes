<?php
namespace Ryssbowh\CraftThemes\models\blockCacheOptions;

use Ryssbowh\CraftThemes\models\BlockStrategyOptions;

/**
 * Global block cache strategy options
 */
class GlobalOptions extends BlockStrategyOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'cachePerAuthenticated' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Cache depends on user authentication')
            ],
            'cachePerUser' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Cache depends on user')
            ],
            'cachePerViewport' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Cache depends on view port (mobile, tablet or desktop)')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'cachePerAuthenticated' => false,
            'cachePerUser' => false,
            'cachePerViewport' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['cachePerAuthenticated', 'cachePerUser', 'cachePerViewport'], 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }
}
