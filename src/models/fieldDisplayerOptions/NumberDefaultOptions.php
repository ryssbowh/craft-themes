<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class NumberDefaultOptions extends FieldDisplayerOptions
{
    /**
     * @var boolean
     */
    public $showPrefix = true;

    /**
     * @var boolean
     */
    public $showSuffix = true;

    /**
     * @var integer
     */
    public $decimals = 0;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['showPrefix', 'showSuffix'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['decimals', 'integer']
        ];
    }
}