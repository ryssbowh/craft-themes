<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class DefaultRedactorOptions extends FieldDisplayerOptions
{
    public $stripped = false;
    public $truncated = '';
    public $ellipsis = '...';

    public function rules()
    {
        return [
            ['truncated', 'integer', 'min' => 1],
            ['stripped', 'boolean'],
            ['ellipsis', 'string']
        ];
    }
}