<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class TitleDefaultOptions extends FieldDisplayerOptions
{
    public $tag = 'h1';
    public $linked = false;

    public function defineRules(): array
    {
        return [
            ['tag', 'string'],
            ['tag', 'in', 'range' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']],
            ['linked', 'boolean']
        ];
    }
}