<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;

/**
 * Handles the dateCreated value of elements
 */
class DateUpdated extends Field
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
        return 'date-updated';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'dateUpdated';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Date updated');
    }
}