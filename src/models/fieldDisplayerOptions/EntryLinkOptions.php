<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class EntryLinkOptions extends FieldDisplayerOptions
{
    /**
     * @var string
     */
    public $label = 'title';

    /**
     * @var string
     */
    public $custom = '';

    /**
     * @var boolean
     */
    public $newTab = false;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['label', 'custom'], 'string'],
            ['newTab', 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['label', 'in', 'range' => ['title', 'custom']],
            ['custom', 'required', 'when' => function ($model) {
                return $model->label == 'custom';
            }],
        ];
    }
}