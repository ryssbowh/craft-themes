<?php

namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

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

    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['set', 'viewMode'], 'required'],
            [['set', 'viewMode'], 'string']
        ]);
    }

    public function getConfig(): array
    {
        return [
            'set' => $this->set, 
            'viewMode' => $this->viewMode
        ];
    }
}
