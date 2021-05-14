<?php

namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

class BlockCurrentUserOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $viewMode;

    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['viewMode'], 'required'],
            [['viewMode'], 'string']
        ]);
    }

    public function getConfig(): array
    {
        return [ 
            'viewMode' => $this->viewMode
        ];
    }
}
