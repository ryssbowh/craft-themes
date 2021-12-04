<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\UserLayout;
use craft\base\Element;

/**
 * Display information about a user (user layouts only)
 */
class UserInfo extends Field
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'user-info';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return $layout instanceof UserLayout;
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
        return \Craft::t('themes', 'User Info');
    }

    /**
     * @inheritDoc
     */
    public function render($value = null): string
    {
        $value = Themes::$plugin->view->renderingElement;
        return Themes::$plugin->view->renderField($this, $value);
    }
}