<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

class TagTitleTitleOptions extends FieldDisplayerOptions
{
    /**
     * @var string
     */
    public $tag = 'h1';

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['tag', 'string'],
            ['tag', 'in', 'range' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p']]
        ];
    }
}