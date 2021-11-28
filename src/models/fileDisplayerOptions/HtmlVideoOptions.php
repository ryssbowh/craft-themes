<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class HtmlVideoOptions extends FileDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'controls' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Show controls')
            ],
            'muted' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Muted')
            ],
            'autoplay' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Autoplay')
            ],
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
            'controls' => false,
            'muted' => false,
            'autoplay' => false,
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
            [['controls', 'muted', 'autoplay'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['width', 'height'], 'number']
        ];
    }
}