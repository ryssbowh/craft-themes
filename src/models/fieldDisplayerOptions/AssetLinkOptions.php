<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class AssetLinkOptions extends FieldDisplayerOptions
{
    public $label = 'title';
    public $custom = '';
    public $newTab = false;
    public $download = false;

    public function defineRules(): array
    {
        return [
            [['label', 'custom'], 'string'],
            [['newTab', 'download'], 'boolean'],
            ['custom', 'required', 'when' => function ($model) {
                return $model->label == 'custom';
            }],
            ['label', 'in', 'range' => ['title', 'filename', 'custom']]
        ];
    }
}