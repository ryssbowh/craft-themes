<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\fields\Assets;

class ElementLinksOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        $options = [
            'label' => [
                'field' => 'select',
                'options' => [
                    'title' => \Craft::t('themes', 'Element title'),
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
        if ($this->displayer->field->craftField instanceof Assets) {
            $options['download'] = [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Download link')
            ];
            $options['label']['options']['filename'] = \Craft::t('themes', 'Filename');
        }
        return $options;
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
        $rules = [
            [['label', 'custom'], 'string'],
            ['newTab', 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['label', 'in', 'range' => array_keys($this->definitions['label']['options'])],
            ['custom', 'required', 'when' => function ($model) {
                return $model->label == 'custom';
            }],
        ];
        if ($this->displayer->field->craftField instanceof Assets) {
            $rules[] = ['download', 'boolean', 'trueValue' => true, 'falseValue' => false];
        }
        return $rules;
    }
}