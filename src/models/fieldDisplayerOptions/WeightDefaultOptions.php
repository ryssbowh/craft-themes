<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class WeightDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'unit' => [
                'field' => 'select',
                'options' => [
                    'g' => 'Grams',
                    'kg' => 'Kilograms',
                    'oz' => 'Ounces',
                    'lb' => 'Pounds',
                    'st' => 'Stones',
                ],
                'required' => true,
                'label' => \Craft::t('themes', 'Unit')
            ],
            'showUnit' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Show unit')
            ],
            'decimals' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Decimals'),
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
            'unit' => 'g',
            'decimals' => 0,
            'showUnit' => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['unit', 'string'],
            ['decimals', 'integer'],
            ['unit', 'in', 'range' => array_keys($this->definitions['unit']['options'])],
            ['showUnit', 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }
}