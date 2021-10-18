<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block search form
 */
class BlockSearchFormOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $action = 'search';

    /**
     * @var string
     */
    public $inputName = 'term';

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['action', 'inputName'], 'string'],
            [['action', 'inputName'], 'required']
        ]);
    }
}
