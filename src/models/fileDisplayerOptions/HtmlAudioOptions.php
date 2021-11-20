<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class HtmlAudioOptions extends FileDisplayerOptions
{
    /**
     * @var boolean
     */
    public $controls = false;

    /**
     * @var boolean
     */
    public $muted = false;

    /**
     * @var boolean
     */
    public $autoplay = false;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['controls', 'muted', 'autoplay'], 'boolean', 'trueValue' => true, 'falseValue' => false],
        ];
    }
}