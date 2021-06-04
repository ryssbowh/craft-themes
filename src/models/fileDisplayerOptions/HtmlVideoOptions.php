<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class HtmlVideoOptions extends FileDisplayerOptions
{
    public $controls = false;
    public $muted = false;
    public $autoplay = false;
    public $width = 500;
    public $height = 500;

    public function defineRules(): array
    {
        return [
            [['controls', 'muted', 'autoplay'], 'boolean'],
            [['width', 'height'], 'number']
        ];
    }
}