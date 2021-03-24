<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class DefaultEmailOptions extends FieldDisplayerOptions
{
    public $linked = true;

    public function rules()
    {
        return [
            ['linked', 'boolean']       
        ];
    }
}