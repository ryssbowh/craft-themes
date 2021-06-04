<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class AssetRenderedOptions extends FieldDisplayerOptions
{
    public $viewModes = [];

    public function defineRules(): array
    {
        return [
            ['viewModes', 'validateViewModes']
        ];
    }

    public function validateViewModes()
    {
        $validViewModes = $this->displayer->getViewModes();
        foreach ($this->viewModes as $volumeUid => $viewModeUid) {
            if (!isset($validViewModes[$volumeUid]['viewModes'][$viewModeUid])) {
               $this->addError('viewMode-'.$volumeUid, \Craft::t('themes', 'View mode is invalid'));
            }
        }
    }
}