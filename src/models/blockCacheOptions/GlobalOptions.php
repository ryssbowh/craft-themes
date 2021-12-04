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
            'duration' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 0,
                'step' => 10,
                'label' => \Craft::t('themes', 'Cache duration (minutes)'),
                'instructions' => \Craft::t('themes', '0 means forever'),
                'size' => 5
            ],
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
            'cachePerViewport' => false,
            'duration' => 0
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['cachePerAuthenticated', 'cachePerUser', 'cachePerViewport'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['duration', 'integer'],
            ['duration', 'required']
        ];
    }
}
