<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class CategoryListOptions extends FieldDisplayerOptions
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
            ['linked', 'boolean']
        ];
    }
}