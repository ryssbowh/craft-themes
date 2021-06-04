<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class CategoryListOptions extends FieldDisplayerOptions
{
    public $linked = false;

    public function defineRules(): array
    {
        return [
            ['linked', 'boolean']
        ];
    }
}