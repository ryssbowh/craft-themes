<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class ImageTransformOptions extends FileDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'transform' => [
                'field' => 'select',
                'options' => array_merge($this->getDisplayer()->getImageTransforms(), ['_custom' => \Craft::t('themes', 'Custom')]),
                'label' => \Craft::t('themes', 'Transform')
            ],
            'custom' => [
                'field' => 'text',
                'instructions' => \Craft::t('themes', 'Enter a json list of options to transform the image, example: {"width": 300, "height": 300}'),
                'label' => \Craft::t('themes', 'Custom')
            ],
            'sizes' => [
                'field' => 'text',
                'instructions' => \Craft::t('themes', 'Enter a json list of options to generate different sizes (srcset), example: ["1.5x", "2x", "3x"]'),
                'label' => \Craft::t('themes', 'Custom sizes')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'transform' => null,
            'custom' => '{}',
            'sizes' => '[]',
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['transform', 'custom', 'sizes'], 'string'],
            ['transform', 'required'],
            ['custom', 'required', 'when' => function ($model) {
                return $model->transform == '_custom';
            }],
            ['custom', 'validateCustom'],
            ['sizes', 'validateSizes'],
            ['transform', 'in', 'range' => array_keys($this->definitions['transform']['options'])]
        ];
    }

    /**
     * Validates custom attribute
     */
    public function validateCustom()
    {
        if (!json_decode($this->custom)) {
            $this->addError('custom', \Craft::t('themes', 'Invalid JSON string'));
        }
    }

    /**
     * Validates custom attribute
     */
    public function validateSizes()
    {
        if ($this->sizes and !json_decode($this->sizes)) {
            $this->addError('sizes', \Craft::t('themes', 'Invalid JSON string'));
        }
    }
}