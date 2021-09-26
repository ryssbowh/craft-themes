<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class TitleDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @var string
     */
    public $tag = 'h1';

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
            ['tag', 'string'],
            ['tag', 'in', 'range' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p']],
            ['linked', 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }
}