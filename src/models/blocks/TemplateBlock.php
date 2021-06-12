<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockOptions\BlockTemplateOptions;

class TemplateBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'Template';

    /**
     * @var string
     */
    public $smallDescription = 'A custom template';

    /**
     * @var string
     */
    public $longDescription = 'Define the template rendering this block in the options';

    /**
     * @var string
     */
    public static $handle = 'template';

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptions
    {
        return new BlockTemplateOptions;
    }
}
