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
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['newTab', 'boolean']
        ];
    }
}