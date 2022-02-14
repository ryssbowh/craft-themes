<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class HtmlAudioOptions extends FileDisplayerOptions
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
            'autoplay' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['controls', 'muted', 'autoplay'], 'boolean', 'trueValue' => true, 'falseValue' => false],
        ];
    }
}