<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class EntryRenderedOptions extends FieldDisplayerOptions
{
    public $viewModes = [];

    public function getTheme()
    {
        return $this->_field->layout()->theme;
    }

    public function rules()
    {
        return [
            ['viewModes', 'each', 'rule' => ['string']]
        ];
    }

    public function getViewModes(): array
    {
        $sources = $this->_field->craftField()->sources;
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

    public function getAllSectionsViewModes(): array
    {
        $sections = \Craft::$app->sections->getAllSections();
        $viewModes = [];
        foreach ($sections as $section) {
            foreach ($section->getEntryTypes() as $type) {
                $layout = Themes::$plugin->layouts->get($this->getTheme(), $type->uid);
                $viewModes2 = [];
                foreach ($layout->getViewModes() as $viewMode) {
                    $viewModes2[$viewMode->handle] = $viewMode->name;
                }
                $viewModes[$type->uid] = [
                    'type' => $type->name,
                    'viewModes' => $viewModes2
                ];
            }
        }
        return $viewModes;
    }

    public function getSingleEntriesViewModes(): array
    {
        $sections = \Craft::$app->sections->getSectionsByType('single');
        $viewModes = [];
        foreach ($sections as $section) {
            $type = $section->getEntryTypes()[0];
            $layout = Themes::$plugin->layouts->get($this->getTheme(), $type->uid);
            $viewModes2 = [];
            foreach ($layout->getViewModes() as $viewMode) {
                $viewModes2[$viewMode->handle] = $viewMode->name;
            }
            $viewModes[$type->uid] = [
                'type' => $type->name,
                'viewModes' => $viewModes2
            ];
        }
        return $viewModes;
    }

    public function getEntryViewModes(string $uid): array
    {
        $section = \Craft::$app->sections->getSectionByUid($uid);
        $type = $section->getEntryTypes()[0];
        $layout = Themes::$plugin->layouts->get($this->getTheme(), $type->uid);
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