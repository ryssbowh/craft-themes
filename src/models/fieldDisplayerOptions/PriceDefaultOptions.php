<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class PriceDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'display' => [
                'field' => 'lightswitch',
                'onLabel' => \Craft::t('themes', 'Display sale price'),
                'offLabel' => \Craft::t('themes', 'Display full price'),
                'label' => \Craft::t('themes', 'Display')
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'display' => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['display', 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }
}