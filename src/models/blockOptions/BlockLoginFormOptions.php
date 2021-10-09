<?php
namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

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
