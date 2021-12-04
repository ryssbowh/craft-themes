<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;

/**
 * Handles the dateUpdated value of elements
 */
class DateCreated extends Field
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
        return 'date-created';
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
        return 'dateCreated';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Date created');
    }

    /**
     * @inheritDoc
     */
    public function render($value = null): string
    {
        $value = Themes::$plugin->view->renderingElement->dateCreated;
        return Themes::$plugin->view->renderField($this, $value);
    }
}