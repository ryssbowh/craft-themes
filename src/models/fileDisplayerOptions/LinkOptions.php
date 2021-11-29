<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class LinkOptions extends FileDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'label' => [
                'field' => 'select',
                'required' => true,
                'options' => [
                    'title' => \Craft::t('themes', 'Asset title'),
                    'filename' => \Craft::t('themes', 'File name'),
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
            'download' => false,
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['label', 'required'],
            [['label', 'custom'], 'string'],
            [['newTab', 'download'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['custom', 'required', 'when' => function ($model) {
                return $model->label == 'custom';
            }],
            ['label', 'in', 'range' => ['title', 'custom', 'filename']]
        ];
    }
}