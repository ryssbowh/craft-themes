<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block global
 */
class BlockGlobalOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $set;

    /**
     * @var string
     */
    public $viewMode;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['set', 'viewMode'], 'required'],
            [['set', 'viewMode'], 'string']
        ]);
    }
}
