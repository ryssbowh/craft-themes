<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class RedactorTrimmedOptions extends FieldDisplayerOptions
{
    public $linked = false;
    public $truncated = '';
    public $ellipsis = '...';

    public function defineRules(): array
    {
        return [
            ['truncated', 'required'],
            ['truncated', 'integer', 'min' => 1],
            ['linked', 'boolean'],
            ['ellipsis', 'string']
        ];
    }
}