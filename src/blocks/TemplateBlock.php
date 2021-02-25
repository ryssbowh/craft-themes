<?php 

namespace Ryssbowh\CraftThemes\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockTemplateOptions;
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
     * @var boolean
     */
    public $hasOptions = true;

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new BlockTemplateOptions;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/block-options/template', [
            'options' => $this->getOptions(),
        ]);
    }
}
