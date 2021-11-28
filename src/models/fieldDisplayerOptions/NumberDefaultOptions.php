<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class NumberDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'showPrefix' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Show prefix')
            ],
            'showSuffix' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Show suffix')
            ],
            'decimals' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 0,
                'step' => 1
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'decimals' => 0,
            'showPrefix' => false,
            'showSuffix' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['showPrefix', 'showSuffix'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['decimals', 'integer']
        ];
    }
}