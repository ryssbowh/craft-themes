<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;
use craft\base\Element;

class File extends Field
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'file';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return ($layout instanceof VolumeLayout);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'file';
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
        return \Craft::t('themes', 'File');
    }
}