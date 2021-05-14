<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockTemplateOptions;
use craft\base\Model;

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
    public function getOptionsModel(): Model
    {
        return new BlockTemplateOptions;
    }
}
