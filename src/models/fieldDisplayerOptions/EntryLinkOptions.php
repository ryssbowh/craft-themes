<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class EntryLinkOptions extends FieldDisplayerOptions
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
                    'title' => \Craft::t('themes', 'Entry title'),
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
            'newTab' => false
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
        ];
    }
}