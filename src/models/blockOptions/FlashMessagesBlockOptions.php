<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block login form
 */
class FlashMessagesBlockOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $removeMessages = true;

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
