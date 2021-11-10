<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class DateOptions extends FieldDisplayerOptions
{
    /**
     * @var string
     */
    public $format = 'd/m/Y';

    /**
     * @var string
     */
    public $custom = '';

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['format', 'custom'], 'string'],
            ['format', 'required'],
            ['format', 'in', 'range' => array_keys($this->formats)],
            ['custom', 'required', 'when' => function ($model) {
                return $model->format == 'custom';
            }],
        ];
    }

    /**
     * Get available formats
     * 
     * @return array
     */
    public function getFormats(): array
    {
        return $this->displayer->formats;
    }
}