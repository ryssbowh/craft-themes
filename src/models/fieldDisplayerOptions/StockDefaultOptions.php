<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class StockDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'display' => [
                'field' => 'lightswitch',
                'onLabel' => \Craft::t('themes', 'Display In stock/Out of stock'),
                'offLabel' => \Craft::t('themes', 'Display stock number'),
                'label' => \Craft::t('themes', 'Display')
            ],
            'inStockLabel' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'In stock label')
            ],
            'outStockLabel' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Out of stock label')
            ],
            'unlimitedStockLabel' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Unlimited stock label')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'display' => false,
            'inStockLabel' => \Craft::t('themes', 'In stock'),
            'outStockLabel' => \Craft::t('themes', 'Out of stock'),
            'unlimitedStockLabel' => \Craft::t('themes', 'Unlimited'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['display', 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['inStockLabel', 'outStockLabel', 'unlimitedStockLabel'], 'string']
        ];
    }
}