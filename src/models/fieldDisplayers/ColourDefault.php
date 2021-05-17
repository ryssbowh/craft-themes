<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\fields\Color;

class ColourDefault extends FieldDisplayer
{
    public static $handle = 'colour_default';

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return Color::class;
    }
}