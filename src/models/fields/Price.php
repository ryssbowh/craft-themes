<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\VariantLayout;

/**
 * Handles the price of variants or products
 */
class Price extends Field
{       
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'price';
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
        return 'price';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Price');
    }
}