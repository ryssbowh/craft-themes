<?php
namespace Ryssbowh\CraftThemesTests\themes\child\models\blockProviders;

use Ryssbowh\CraftThemes\base\BlockProvider;
use Ryssbowh\CraftThemesTests\themes\child\models\blocks\FooterMenu;
use Ryssbowh\CraftThemesTests\themes\child\models\blocks\MainMenu;
use Ryssbowh\CraftThemesTests\themes\child\models\blocks\PoweredBy;

class TestBlockProvider extends BlockProvider
{
    /**
     * @var array
     */
    protected $_definedBlocks = [
        PoweredBy::class,
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