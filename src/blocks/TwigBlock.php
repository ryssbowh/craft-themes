<?php 

namespace Ryssbowh\CraftThemes\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockTemplateOptions;
use Ryssbowh\CraftThemes\models\BlockTwigOptions;
use craft\base\Model;

class TwigBlock extends Block
{
	public $name = 'Twig';

    public $smallDescription = 'Custom twig code';

    public $longDescription = 'Define custom twig to render this block. Variable `block` is available';

	public static $handle = 'twig';

    public $hasOptions = true;

    public function getOptionsModel(): Model
    {
        return new BlockTwigOptions;
    }

	public function getOptionsHtml(): string
	{
		return \Craft::$app->view->renderTemplate('themes/block-options/twig', [
            'options' => $this->getOptions(),
        ]);
	}
}
