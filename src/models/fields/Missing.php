<?php
namespace Ryssbowh\CraftThemes\models\fields;

use craft\fields\MissingField;

/**
 * Handles a missing field
 */
class Missing extends CraftField
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'missing';
    }

    /**
     * @inheritDoc
     */
    public static function forField(): string
    {
        return MissingField::class;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayName(): string
    {
        return \Craft::t('themes', 'Missing field');
    }

    /**
     * @inheritDoc
     */
    public function getAvailableDisplayers(): array
    {
        return [];
    }
}