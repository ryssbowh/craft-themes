<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class EmailDefaultOptions extends FieldDisplayerOptions
{
    public $linked = true;

    public function defineRules(): array
    {
        return [
            ['linked', 'boolean']       
        ];
    }
}