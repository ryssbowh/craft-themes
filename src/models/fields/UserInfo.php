<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\UserLayout;
use craft\base\Element;

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
    public function render(Element $element): string
    {
        return Themes::$plugin->view->renderField($this, $element, $element);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'User Info');
    }
}