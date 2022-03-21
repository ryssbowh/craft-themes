<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class AllowedQtyDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'showMin' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Show minimum quantity')
            ],
            'showMax' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Show maximum quantity')
            ],
            'minQtyLabel' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Minimum quantity label'),
                'required' => true
            ],
            'maxQtyLabel' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Maximum quantity label'),
                'required' => true
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'maxQtyLabel' => \Craft::t('themes', 'Maximum quantity'),
            'minQtyLabel' => \Craft::t('themes', 'Minimum quantity'),
            'showMin' => true,
            'showMax' => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['showMin', 'showMax'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['minQtyLabel', 'maxQtyLabel'], 'string'],
            [['minQtyLabel', 'maxQtyLabel'], 'required'],
        ];
    }
}