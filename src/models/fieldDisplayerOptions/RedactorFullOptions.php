<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class RedactorFullOptions extends FieldDisplayerOptions
{
    public $stripped = false;

    public function defineRules(): array
    {
        return [
            ['stripped', 'boolean'],
        ];
    }
}