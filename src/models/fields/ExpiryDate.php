<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\ProductLayout;

/**
 * Handles the dateCreated value of elements
 */
class ExpiryDate extends Field
{
    /**
     * @var boolean
     */
    public $hidden = true;

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'expiry-date';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        if ($layout instanceof ProductLayout) {
            return true;
        }
        if ($layout instanceof EntryLayout) {
            return ($layout->element->section->type != 'single');
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'expiryDate';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Expiry date');
    }
}