<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;

/**
 * Handles the element url for entries and categories
 */
class ElementUrl extends Field
{       
    /**
     * @inheritDoc
     */
    public $hidden = true;

    /**
     * @inheritDoc
     */
    public $labelHidden = true;

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'element-url';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return $layout instanceof EntryLayout or $layout instanceof CategoryLayout or $layout instanceof VolumeLayout;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'url';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Element url');
    }
}