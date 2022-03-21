<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\VariantLayout;

/**
 * Handles the min and max quantity of a variant
 */
class AllowedQty extends Field
{       
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'allowed-qty';
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return $layout instanceof VariantLayout;
    }

    /**
     * @inheritDoc
     */
    public function getRenderingValue()
    {
        $variant = Themes::$plugin->view->renderingElement;
        return [
            'min' => $variant->minQty,
            'max' => $variant->maxQty
        ];
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'quantity';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Allowed quantity');
    }
}