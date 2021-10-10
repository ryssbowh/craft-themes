<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\base\Model;
use craft\fields\MultiSelect;

/**
 * Renders a multiselect field
 */
class MultiSelectDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'multiselect_default';

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
        return MultiSelect::class;
    }
}