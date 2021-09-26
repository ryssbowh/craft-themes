<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class HtmlVideoOptions extends FileDisplayerOptions
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
     * @var integer
     */
    public $width = 500;

    /**
     * @var integer
     */
    public $height = 500;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['controls', 'muted', 'autoplay'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['width', 'height'], 'number']
        ];
    }
}