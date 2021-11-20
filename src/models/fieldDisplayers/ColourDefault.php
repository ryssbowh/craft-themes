<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\fields\Color;

/**
 * Renders a colour field
 */
class ColourDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'colour_default';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return Color::class;
    }
}