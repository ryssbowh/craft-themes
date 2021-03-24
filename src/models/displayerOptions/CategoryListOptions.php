<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class CategoryListOptions extends FieldDisplayerOptions
{
    public $linked = false;

    public function rules()
    {
        return [
            ['linked', 'boolean']
        ];
    }
}