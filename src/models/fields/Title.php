<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\ProductLayout;
use Ryssbowh\CraftThemes\models\layouts\TagLayout;
use Ryssbowh\CraftThemes\models\layouts\VariantLayout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;

/**
 * The field Title is added to all entry types, category groups and volume layouts automatically
 */
class Title extends Field
{
    /**
     * @var boolean
     */
    public $labelHidden = true;
    
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'title';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        if ($layout instanceof EntryLayout or 
            $layout instanceof CategoryLayout or 
            $layout instanceof VolumeLayout or 
            $layout instanceof ProductLayout or 
            $layout instanceof VariantLayout
        ) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'title';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'Title');
    }
}