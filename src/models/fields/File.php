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
    public function eagerLoad(string $prefix = '', int $level = 0, array &$dependencies = []): array
    {
        //Prefix can't be null here as this must be called from another field
        if (!$this->displayer or !$prefix) {
            return [];
        }
        if ($level >= Themes::$plugin->settings->maxEagerLoadLevel) {
            \Craft::info("Maximum eager loaging level (" . Themes::$plugin->settings->maxEagerLoadLevel . ') reached', __METHOD__);
            return [];
        }
        //This is a edge case as this field isn't defined on the asset, so we don't have a field name.
        //The field name will be whatever prefix we were given, just need to remove the dot
        $with = $prefix ? substr($prefix, 0, -1) : $prefix;
        return $this->displayer->eagerLoad([$with], $with . '.', $level);
    }
}