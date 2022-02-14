<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class IframeOptions extends FileDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'width' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 0,
                'step' => 100,
                'label' => \Craft::t('themes', 'Width')
            ],
            'height' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 0,
                'step' => 100,
                'label' => \Craft::t('themes', 'Height')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'width' => 500,
            'height' => 500
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['width', 'height'], 'number']
        ];
    }
}