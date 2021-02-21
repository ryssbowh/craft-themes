<?php 

namespace Ryssbowh\CraftThemes\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockTemplateOptions;
use craft\base\Model;

class TemplateBlock extends Block
{
	public $name = 'Template';

    public $smallDescription = 'A custom template';

    public $longDescription = 'Define the template rendering this block in the options';

	public static $handle = 'template';

    public $hasOptions = true;

    public function getOptionsModel(): Model
    {
        return new BlockTemplateOptions;
    }

	public function getOptionsHtml(): string
	{
		return \Craft::$app->view->renderTemplate('themes/block-options/template', [
            'options' => $this->getOptions(),
        ]);
	}
}
