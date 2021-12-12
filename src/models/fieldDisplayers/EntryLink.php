<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EntryLinkOptions;
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
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

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
    public static function getFieldTargets(): array
    {
        return [Entries::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return EntryLinkOptions::class;
    }
}