<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class IframeOptions extends FileDisplayerOptions
{
    public $width = 500;
    public $height = 500;

    public function defineRules(): array
    {
        return [
            [['width', 'height'], 'number']
        ];
    }
}