<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class AssetRenderFileOptions extends FieldDisplayerOptions
{
    public $viewModes = [];

    public function getTheme()
    {
        return $this->field->layout->theme;
    }

    public function defineRules(): array
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