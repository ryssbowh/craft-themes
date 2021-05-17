<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class UrlDefaultOptions extends FieldDisplayerOptions
{
    public $newTab = true;

    public function defineRules(): array
    {
        return [
            ['newTab', 'boolean']
        ];
    }
}