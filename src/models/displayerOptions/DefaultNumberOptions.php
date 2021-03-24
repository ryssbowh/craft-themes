<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class DefaultNumberOptions extends FieldDisplayerOptions
{
    public $showPrefix = true;
    public $showSuffix = true;
    public $decimals = 0;

    public function rules()
    {
        return [
            [['showPrefix', 'showSuffix'], 'boolean'],
            ['decimals', 'integer']
        ];
    }
}