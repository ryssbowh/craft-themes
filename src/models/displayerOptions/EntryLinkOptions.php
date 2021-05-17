<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class EntryLinkOptions extends FieldDisplayerOptions
{
    public $label = 'title';
    public $custom = '';
    public $newTab = false;

    public function defineRules(): array
    {
        return [
            [['label', 'custom'], 'string'],
            ['newTab', 'boolean'],
            ['label', 'in', 'range' => ['title', 'custom']],
            ['custom', 'required', 'when' => function ($model) {
                return $model->label == 'custom';
            }],
        ];
    }
}