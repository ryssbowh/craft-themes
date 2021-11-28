<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class UserDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'firstName' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Display first name')
            ],
            'lastName' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Display last name')
            ],
            'username' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Display username')
            ],
            'email' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Display email')
            ],
            'linkEmail' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Link email')
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'firstName' => true,
            'lastName' => true,
            'username' => false,
            'email' => false,
            'linkEmail' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['firstName', 'lastName', 'email', 'username', 'linkEmail'], 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }
}