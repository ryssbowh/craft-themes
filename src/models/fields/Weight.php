<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\layouts\VariantLayout;

/**
 * Handles the weight of a variant
 */
class Weight extends Field
{       
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'weight';
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
    public function getHandle(): string
    {
        return 'weight';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Weight');
    }
}