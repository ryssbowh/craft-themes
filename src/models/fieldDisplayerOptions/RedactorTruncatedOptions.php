<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class RedactorTruncatedOptions extends FieldDisplayerOptions
{
    public $linked = false;
    public $truncated = 30;
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