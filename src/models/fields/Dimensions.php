<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\VariantLayout;

/**
 * Handles the stock of variants
 */
class Dimensions extends Field
{       
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'dimensions';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        if ($layout instanceof VariantLayout) {
            return $layout->element->hasDimensions;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getRenderingValue()
    {
        $variant = Themes::$plugin->view->renderingElement;
        return [
            'width' => $variant->width,
            'height' => $variant->height,
            'length' => $variant->length
        ];
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'dimensions';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Dimensions');
    }
}