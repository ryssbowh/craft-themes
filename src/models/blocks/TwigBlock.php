<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\TwigBlockOptions;

/**
 * Block displaying some custom twig code
 */
class TwigBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'twig';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Twig');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Custom twig code');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Define custom twig to render this block. Variable `block` will be available');
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return TwigBlockOptions::class;
    }
}
