<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EntryRenderedOptions;
use craft\fields\Entries;

/**
 * Renders an entry field as rendered using a view mode
 */
class EntryRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'entry_rendered';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered as view mode');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return Entries::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return EntryRenderedOptions::class;
    }
}