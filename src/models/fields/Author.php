<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;

class Author extends Field
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'author';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        if ($layout instanceof EntryLayout) {
            return $layout->element->section->type != 'single';
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'author';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Author');
    }
}