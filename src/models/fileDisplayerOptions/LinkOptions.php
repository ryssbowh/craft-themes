<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class LinkOptions extends FileDisplayerOptions
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
     * @var boolean
     */
    public $download = false;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['label', 'custom'], 'string'],
            [['newTab', 'download'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['custom', 'required', 'when' => function ($model) {
                return $model->label == 'custom';
            }],
            ['label', 'in', 'range' => ['title', 'custom', 'filename']]
        ];
    }
}