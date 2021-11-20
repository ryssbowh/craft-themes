<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class CategoryLabelOptions extends FieldDisplayerOptions
{
    /**
     * @var boolean
     */
    public $linked = false;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['linked', 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }
}