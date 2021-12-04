<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\UserLayout;

/**
 * Handles the lastLoginDate value of users
 */
class LastLoginDate extends Field
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
        return 'date-lastlogindate';
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
        return 'lastLoginDate';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Last login date');
    }

    /**
     * @inheritDoc
     */
    public function render($value = null): string
    {
        $value = Themes::$plugin->view->renderingElement->lastLoginDate;
        return Themes::$plugin->view->renderField($this, $value);
    }
}