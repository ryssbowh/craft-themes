<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class EmailDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @var boolean
     */
    public $linked = true;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['linked', 'boolean']       
        ];
    }
}