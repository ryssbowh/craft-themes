<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\MultiSelectLabelOptions;
use craft\base\Model;
use craft\fields\MultiSelect;

/**
 * Renders a multiselect field
 */
class MultiSelectLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'multiselect_label';

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
        return MultiSelect::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return MultiSelectLabelOptions::class;
    }
}