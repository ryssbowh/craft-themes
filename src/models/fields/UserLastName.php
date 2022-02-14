<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\UserLayout;

/**
 * Handles the last name of user layouts
 */
class UserLastName extends Field
{   
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'user-lastname';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return get_class($layout) == UserLayout::class;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'lastName';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Last Name');
    }
}