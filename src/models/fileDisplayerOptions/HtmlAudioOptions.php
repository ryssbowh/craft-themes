<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class HtmlAudioOptions extends FileDisplayerOptions
{
    public $controls = false;
    public $muted = false;
    public $autoplay = false;

    public function defineRules(): array
    {
        return [
            [['controls', 'muted', 'autoplay'], 'boolean'],
        ];
    }
}