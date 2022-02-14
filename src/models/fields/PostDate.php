<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;

/**
 * Handles the postDate value of entry elements
 */
class PostDate extends Field
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
        return 'post-date';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return get_class($layout) == EntryLayout::class;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'postDate';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Date posted');
    }
}