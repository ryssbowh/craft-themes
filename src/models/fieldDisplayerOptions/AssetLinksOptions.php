<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\fields\Assets;

class AssetLinksOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'label' => [
                'field' => 'select',
                'options' => [
                    'title' => \Craft::t('themes', 'Asset title'),
                    'filename' => \Craft::t('themes', 'Filename'),
                    'custom' => \Craft::t('themes', 'Custom'),
                ],
                'label' => \Craft::t('app', 'Label')
            ],
            'custom' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Custom')
            ],
            'newTab' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Open in new tab')
            ],
            'download' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Download link')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'label' => 'title',
            'custom' => '',
            'newTab' => false,
            'download' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['label', 'custom'], 'string'],
            ['newTab', 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['label', 'in', 'range' => array_keys($this->definitions['label']['options'])],
            ['custom', 'required', 'when' => function ($model) {
                return $model->label == 'custom';
            }],
            ['download', 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }
}