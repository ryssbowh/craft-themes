<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayerOptions;

use Ryssbowh\CraftThemes\models\FileDisplayerOptions;

class IframeOptions extends FileDisplayerOptions
{
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
            [['width', 'height'], 'number']
        ];
    }
}