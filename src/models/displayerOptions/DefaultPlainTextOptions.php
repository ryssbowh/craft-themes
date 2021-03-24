<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use craft\base\Model;

class DefaultPlainTextOptions extends Model
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