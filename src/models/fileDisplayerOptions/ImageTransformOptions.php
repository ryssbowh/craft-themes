<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class ImageTransformOptions extends FileDisplayerOptions
{
    /**
     * @var string
     */
    public $transform;

    /**
     * @var string
     */
    public $custom;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['transform', 'custom'], 'string'],
            ['custom', 'required', 'when' => function ($model) {
                return $model->transform == '_custom';
            }],
            ['custom', 'validateCustom'],
            ['transform', 'in', 'range' => array_merge(array_keys($this->displayer->getImageTransforms()), ['_custom'])]
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
}