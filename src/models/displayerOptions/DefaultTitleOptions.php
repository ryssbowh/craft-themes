<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class DefaultTitleOptions extends FieldDisplayerOptions
{
    public $tag = 'h1';
    public $linked = false;

    public function getTags()
    {
        return ['h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6'];
    }

    public function rules()
    {
        return [
            ['tag', 'string'],
            ['tag', 'in', 'range' => $this->getTags()],
            ['linked', 'boolean']
        ];
    }
}