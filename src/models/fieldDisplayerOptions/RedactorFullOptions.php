<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class RedactorFullOptions extends FieldDisplayerOptions
{
    /**
     * @var boolean
     */
    public $stripped = false;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['stripped', 'boolean', 'trueValue' => true, 'falseValue' => false],
        ];
    }
}