<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class AssetLinkOptions extends FieldDisplayerOptions
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
                    'filename' => \Craft::t('themes', 'File name'),
                    'custom' => \Craft::t('themes', 'Custom'),
                ],
                'required' => true,
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
            [['newTab', 'download'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['custom', 'required', 'when' => function ($model) {
                return $model->label == 'custom';
            }],
            ['label', 'in', 'range' => array_keys($this->definitions['label']['options'])]
        ];
    }
}