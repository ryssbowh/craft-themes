<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class RedactorFullOptions extends FieldDisplayerOptions
{
    public $stripped = false;

    public function rules()
    {
        return [
            ['stripped', 'boolean'],
        ];
    }
}