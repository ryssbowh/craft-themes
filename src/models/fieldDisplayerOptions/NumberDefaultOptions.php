<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class NumberDefaultOptions extends FieldDisplayerOptions
{
    public $showPrefix = true;
    public $showSuffix = true;
    public $decimals = 0;

    public function defineRules(): array
    {
        return [
            [['showPrefix', 'showSuffix'], 'boolean'],
            ['decimals', 'integer']
        ];
    }
}