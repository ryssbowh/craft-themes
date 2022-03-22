<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class DimensionsDefaultOptions extends FieldDisplayerOptions
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
                    'mm' => 'Millimetres',
                    'cm' => 'Centimeters',
                    'm' => 'Meters',
                    'ft' => 'Feet',
                    'in' => 'Inches',
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
            'decimals' => 2,
            'unit' => 'mm',
            'showUnit' => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['showUnit', 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['decimals', 'integer']
        ];
    }
}