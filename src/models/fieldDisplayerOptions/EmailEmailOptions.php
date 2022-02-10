<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class EmailEmailOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'linked' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Output as link')
            ],
            'label' => [
                'field' => 'text',
                'label' => \Craft::t('themes', 'Custom label'),
                'placeholder' => \Craft::t('themes', 'Take the email itself if left blank')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'linked' => true,
            'label' => '',
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['linked', 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['label', 'trim']
        ];
    }
}