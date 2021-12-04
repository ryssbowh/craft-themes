<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;

/**
 * The field userInfo is added to all user layouts automatically
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

    /**
     * @inheritDoc
     */
    public function render($value = null): string
    {
        $value = Themes::$plugin->view->renderingElement->dateUpdated;
        return Themes::$plugin->view->renderField($this, $value);
    }
}