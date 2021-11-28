<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the flash messages block
 */
class FlashMessagesBlockOptions extends BlockOptions
{
    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return [
            'removeMessages' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('app', 'Remove messages from session')
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'removeMessages' => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['removeMessages', 'boolean', 'trueValue' => true, 'falseValue' => false],
        ]);
    }
}
