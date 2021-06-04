<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class LinkOptions extends FileDisplayerOptions
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
            ['label', 'in', 'range' => ['title', 'custom', 'filename']]
        ];
    }
}