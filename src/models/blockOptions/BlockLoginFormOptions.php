<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

/**
 * Options for the block login form
 */
class BlockLoginFormOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $onlyIfNotAuthenticated = true;

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
