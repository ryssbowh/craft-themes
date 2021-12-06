<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\helpers\ViewModesHelper;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EntryRenderedOptions;

/**
 * Renders an entry field as rendered using a view mode
 */
class EntryRendered extends EntryLink
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
    public static function isDefault(string $fieldClass): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return EntryRenderedOptions::class;
    }

    /**
     * Get view modes available, based on the field entry sections
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        return ViewModesHelper::getSectionsViewModes($this->field->craftField, $this->getTheme());
    }
}