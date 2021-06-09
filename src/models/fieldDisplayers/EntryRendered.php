<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EntryRenderedOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Model;
use craft\fields\Entries;

class EntryRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'entry_rendered';

    /**
     * @inheritDoc
     */
    public $hasOptions = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
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
    public function getOptionsModel(): Model
    {
        return new EntryRenderedOptions;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['viewModes']);
    }

    /**
     * Get view modes available, based on this displayer's field entry sections
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        $sources = $this->field->craftField->sources;
        if ($sources == '*') {
            return $this->getAllSectionsViewModes();
        }
        $viewModes = [];
        foreach ($sources as $source) {
            if ($source == 'singles') {
                $viewModes = $viewModes + $this->getSingleEntriesViewModes();
            } else {
                $elems = explode(':', $source);
                $viewModes = $viewModes + $this->getEntryViewModes($elems[1]);
            }
        }
        return $viewModes;
    }

    /**
     * Get all view modes defined for this displayer's field (all sections)
     * 
     * @return array
     */
    protected function getAllSectionsViewModes(): array
    {
        $sections = \Craft::$app->sections->getAllSections();
        $viewModes = [];
        foreach ($sections as $section) {
            foreach ($section->getEntryTypes() as $type) {
                $layout = Themes::$plugin->layouts->get($this->getTheme(), LayoutService::ENTRY_HANDLE, $type->uid);
                $viewModes2 = [];
                foreach ($layout->getViewModes() as $viewMode) {
                    $viewModes2[$viewMode->uid] = $viewMode->name;
                }
                $viewModes[$type->uid] = [
                    'type' => $type->name,
                    'viewModes' => $viewModes2
                ];
            }
        }
        return $viewModes;
    }

    /**
     * Get all view modes defined for this displayer's field (single sections)
     * 
     * @return array
     */
    protected function getSingleEntriesViewModes(): array
    {
        $sections = \Craft::$app->sections->getSectionsByType('single');
        $viewModes = [];
        foreach ($sections as $section) {
            $type = $section->getEntryTypes()[0];
            $layout = Themes::$plugin->layouts->get($this->getTheme(), LayoutService::ENTRY_HANDLE, $type->uid);
            $viewModes2 = [];
            foreach ($layout->getViewModes() as $viewMode) {
                $viewModes2[$viewMode->uid] = $viewMode->name;
            }
            $viewModes[$type->uid] = [
                'type' => $type->name,
                'viewModes' => $viewModes2
            ];
        }
        return $viewModes;
    }

    /**
     * Get all view modes defined for this displayer's field (for one section uid)
     * 
     * @return array
     */
    protected function getEntryViewModes(string $uid): array
    {
        $section = \Craft::$app->sections->getSectionByUid($uid);
        $type = $section->getEntryTypes()[0];
        $layout = Themes::$plugin->layouts->get($this->getTheme(), LayoutService::ENTRY_HANDLE, $type->uid);
        $viewModes = [];
        foreach ($layout->getViewModes() as $viewMode) {
            $viewModes[$viewMode->handle] = $viewMode->name;
        }
        return [$type->uid => [
            'type' => $type->name,
            'viewModes' => $viewModes
        ]];
    }
}