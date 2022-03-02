<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\VariantLayout;

/**
 * Handles the SKU of variants or products
 */
class Sku extends Field
{       
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'sku';
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
    public function getHandle(): string
    {
        return 'sku';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'SKU');
    }
}