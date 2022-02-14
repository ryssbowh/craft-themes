<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class RedactorTruncatedOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'truncated' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 0,
                'step' => 10,
                'required' => true,
                'label' => \Craft::t('themes', 'Character limit')
            ],
            'ellipsis' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Ellipsis')
            ],
            'linked' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Link ellipsis to element')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'linked' => false,
            'truncated' => 100,
            'ellipsis' => '...'
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['truncated', 'required'],
            ['truncated', 'integer', 'min' => 1],
            ['linked', 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['ellipsis', 'string']
        ];
    }
}