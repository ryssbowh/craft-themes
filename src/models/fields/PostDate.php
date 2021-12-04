<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;

/**
 * The field userInfo is added to all user layouts automatically
 */
class PostDate extends Field
{
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
        return true;
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

    /**
     * @inheritDoc
     */
    public function render($value = null): string
    {
        $value = Themes::$plugin->view->renderingElement;
        return Themes::$plugin->view->renderField($this, $value);
    }
}