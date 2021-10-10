<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EntryLinkOptions;
use craft\base\Model;
use craft\fields\Entries;

/**
 * Renders an entry field as links
 */
class EntryLink extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'entry_link';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Link to entry');
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
        return EntryLinkOptions::class;
    }
}