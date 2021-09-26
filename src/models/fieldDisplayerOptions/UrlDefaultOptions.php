<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class UrlDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @var boolean
     */
    public $newTab = true;

    /**
     * @var string
     */
    public $label;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['newTab', 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['label', 'string']
        ];
    }
}