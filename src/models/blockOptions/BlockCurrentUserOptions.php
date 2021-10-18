<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block current user
 */
class BlockCurrentUserOptions extends BlockOptions
{
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
            [['viewMode'], 'required'],
            [['viewMode'], 'string']
        ]);
    }
}
