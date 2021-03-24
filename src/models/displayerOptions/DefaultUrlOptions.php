<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class DefaultUrlOptions extends FieldDisplayerOptions
{
    public $newTab = true;

    public function rules()
    {
        return [
            ['newTab', 'boolean']
        ];
    }
}