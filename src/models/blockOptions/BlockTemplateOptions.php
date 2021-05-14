<?php

namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

class BlockTemplateOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $template = '';

    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['template', 'string'],
            ['template', 'required']
        ]);
    }
}
