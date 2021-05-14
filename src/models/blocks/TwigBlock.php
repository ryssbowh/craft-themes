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
    public $longDescription = 'Define custom twig to render this block. Variable `block` will be available';

    /**
     * @var string
     */
    public static $handle = 'twig';

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new BlockTwigOptions;
    }
}
