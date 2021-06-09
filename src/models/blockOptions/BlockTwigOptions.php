<?php

namespace Ryssbowh\CraftThemes\models\blockOptions;

use Ryssbowh\CraftThemes\models\BlockOptions;

class BlockTwigOptions extends BlockOptions
{
    /**
     * @var string
     */
    public $twig = '';

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['twig', 'string']
        ]);
    }
}
