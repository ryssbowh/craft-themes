<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\base\Model;
use craft\fields\Dropdown;

class DropdownDefault extends FieldDisplayer
{
    public $handle = 'dropdown_default';

    public $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return Dropdown::class;
    }
}