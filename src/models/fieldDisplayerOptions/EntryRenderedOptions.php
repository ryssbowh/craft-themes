<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\traits\ViewModesOptions;
use craft\elements\Entry;

class EntryRenderedOptions extends FieldDisplayerOptions
{
    use ViewModesOptions;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return $this->defineViewModesRules();
    }

    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return $this->defineViewModesOptions();
    }

    /**
     * Get the view mode for an entry
     * 
     * @param  Entry $entry
     * @return ?ViewModeInterface $entry
     */
    public function getEntryViewMode(Entry $entry): ?ViewModeInterface
    {
        $uid = $this->_viewModes[$entry->type->uid] ?? false;
        if ($uid) {
            return Themes::$plugin->viewModes->getByUid($uid);
        }
        return null;
    }
}