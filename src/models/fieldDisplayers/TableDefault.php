<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TableDefaultOptions;
use craft\fields\Table;

/**
 * Renders a table field
 */
class TableDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'table_default';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Table');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return Table::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return TableDefaultOptions::class;
    }
}