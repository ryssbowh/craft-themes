<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use craft\base\Model;

class DefaultPlainTextOptions extends FieldDisplayerOptions
{
    public $trimmed = '';
    public $ellipsis = '...';

    public function rules()
    {
        return [
            ['trimmed', 'integer', 'min' => 1],
            ['ellipsis', 'string']
        ];
    }
}