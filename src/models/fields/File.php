<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;
use craft\base\Element;

/**
 * The field File is added to all assets automatically
 */
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
    public function getName(): string
    {
        return \Craft::t('themes', 'File');
    }

    /**
     * @inheritDoc
     */
    public function getRenderingValue()
    {
        return Themes::$plugin->view->renderingElement;
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(string $prefix = '', int $level = 0): array
    {
        if (!$this->displayer) {
            return [];
        }
        if ($level >= Themes::$plugin->settings->maxEagerLoadLevel) {
            \Craft::info("Maximum eager loaging level (" . Themes::$plugin->settings->maxEagerLoadLevel . ') reached', __METHOD__);
            return [];
        }
        $with = substr($prefix, 0, -1);
        return $this->displayer->eagerLoad([$with], $with . '.', $level);
    }
}