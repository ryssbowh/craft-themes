<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class RedactorTruncatedOptions extends FieldDisplayerOptions
{
    /**
     * @var boolean
     */
    public $linked = true;

    /**
     * @var integer
     */
    public $truncated = 30;

    /**
     * @var string
     */
    public $ellipsis = '...';

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['truncated', 'required'],
            ['truncated', 'integer', 'min' => 1],
            ['linked', 'boolean', 'trueValue' => true, 'falseValue' => false],
            ['ellipsis', 'string']
        ];
    }
}