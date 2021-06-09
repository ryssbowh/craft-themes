<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class TimeDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @var string
     */
    public $format = 'H:i:s';

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
            ['custom', 'required', 'when' => function ($model) {
                return $model->format == 'custom';
            }],
        ];
    }
}