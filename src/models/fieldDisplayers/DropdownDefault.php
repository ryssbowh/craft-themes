<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\base\Model;
use craft\fields\Dropdown;

class DropdownDefault extends FieldDisplayer
{
    public static $handle = 'dropdown_default';

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return Dropdown::class;
    }
}