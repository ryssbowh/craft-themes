<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class RedactorFullOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'stripped' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Strip HTML tags')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'stripped' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['stripped', 'boolean', 'trueValue' => true, 'falseValue' => false],
        ];
    }
}