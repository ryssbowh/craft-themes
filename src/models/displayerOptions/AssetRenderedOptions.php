<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class AssetRenderedOptions extends FieldDisplayerOptions
{
    public $viewModes = [];

    public function getTheme()
    {
        return $this->field->layout->theme;
    }

    public function getViewModes(): array
    {
        $source = $this->field->craftField->sources;
        $viewModes = [];
        if ($source == '*') {
            $volumes = \Craft::$app->volumes->getAllVolumes();
        } else {
            $volumes = [];
            foreach ($source as $source) {
                $elems = explode(':', $source);
                $volumes[] = \Craft::$app->volumes->getVolumeByUid($elems[1]);        
            }
        }
        foreach ($volumes as $volume) {
            dd($this->getTheme(), LayoutService::VOLUME_HANDLE, $volume->uid);
            $layout = Themes::$plugin->layouts->get($this->getTheme(), LayoutService::VOLUME_HANDLE, $volume->uid);
            dd($layout);
            $volumeViewModes = [];
            foreach ($layout->viewModes as $viewMode) {
                $volumeViewModes[$viewMode->uid] = $viewMode->name;
            }
            $viewModes[$volume->uid] = [
                'label' => $volume->name,
                'viewModes' => $volumeViewModes
            ];
        }

        return $viewModes;
    }

    public function rules()
    {
        return [
            ['viewModes', 'validateViewModes']
        ];
    }

    public function validateViewModes()
    {
        $validViewModes = $this->getViewModes();
        foreach ($this->viewModes as $volumeUid => $viewModeUid) {
            if (!isset($validViewModes[$volumeUid]['viewModes'][$viewModeUid])) {
               $this->addError('viewMode-'.$volumeUid, \Craft::t('themes', 'View mode is invalid'));
            }
        }
    }
}