<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the login form block
 */
class LoginFormBlockOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'onlyIfNotAuthenticated' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('app', 'Show only if the user is not authenticated')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'onlyIfNotAuthenticated' => true,
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['onlyIfNotAuthenticated', 'boolean', 'trueValue' => true, 'falseValue' => false],
        ]);
    }
}
