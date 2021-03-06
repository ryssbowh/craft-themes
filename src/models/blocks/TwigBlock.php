<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockTwigOptions;
use craft\base\Model;

class TwigBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'Twig';

    /**
     * @var string
     */
    public $smallDescription = 'Custom twig code';

    /**
     * @var string
     */
    public $longDescription = 'Define custom twig to render this block. Variable `block` is available';

    /**
     * @var string
     */
    public static $handle = 'twig';

    /**
     * @var boolean
     */
    public $hasOptions = true;

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new BlockTwigOptions;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate(
            'themes/block-options/twig', [
                'options' => $this->getOptions(),
            ]
        );
    }
}
