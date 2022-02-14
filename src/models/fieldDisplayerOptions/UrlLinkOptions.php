<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class UrlLinkOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'label' => [
                'field' => 'text',
                'label' => \Craft::t('app', 'Label'),
                'instructions' => \Craft::t('themes', 'Leave blank to use the url itself')
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
            'label' => '',
            'newTab' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['newTab', 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['label', 'string']
        ];
    }
}