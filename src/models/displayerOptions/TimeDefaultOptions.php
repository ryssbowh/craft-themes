<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class TimeDefaultOptions extends FieldDisplayerOptions
{
    public $format = 'H:i:s';
    public $custom = '';

    public function defineRules(): array
    {
        return [
            [['format', 'custom'], 'string'],
            ['format', 'required'],
            ['custom', 'required', 'when' => function ($model) {
                return $model->format == 'custom';
            }],
        ];
    }
}