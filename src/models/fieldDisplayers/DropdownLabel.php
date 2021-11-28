<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DropdownLabelOptions;
use craft\base\Model;
use craft\fields\Dropdown;

/**
 * Renders a dropdown field
 */
class DropdownLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'dropdown_label';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Label');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return Dropdown::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return DropdownLabelOptions::class;
    }
}