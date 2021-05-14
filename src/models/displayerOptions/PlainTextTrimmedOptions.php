<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\base\Model;

class PlainTextTrimmedOptions extends FieldDisplayerOptions
{
    public $truncated = '';
    public $ellipsis = '...';
    public $linked = false;

    public function rules()
    {
        return [
            ['linked', 'boolean'],
            ['truncated', 'required'],
            ['truncated', 'integer', 'min' => 1],
            ['ellipsis', 'string']
        ];
    }
}