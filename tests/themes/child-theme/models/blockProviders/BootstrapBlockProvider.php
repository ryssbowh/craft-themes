<?php 

namespace Ryssbowh\tests\themes\child\models\blockProviders;

use Ryssbowh\tests\themes\child\models\blocks\FooterMenu;
use Ryssbowh\tests\themes\child\models\blocks\MainMenu;
use Ryssbowh\tests\themes\child\models\blocks\PoweredByBootstrap;
use Ryssbowh\CraftThemes\models\BlockProvider;

class BootstrapBlockProvider extends BlockProvider
{
    /**
     * @var array
     */
    protected $_definedBlocks = [
        PoweredByBootstrap::class,
        MainMenu::class,
        FooterMenu::class,
    ];

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Child Theme');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'child';
    }
}