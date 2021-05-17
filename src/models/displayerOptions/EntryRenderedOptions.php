<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class EntryRenderedOptions extends FieldDisplayerOptions
{
    public $viewModes = [];

    public function defineRules(): array
    {
        return [
            ['viewModes', 'validateViewModes', 'skipOnEmpty' => false]
        ];
    }

    public function validateViewModes()
    {
        $validViewModes = $this->displayer->getViewModes();
        foreach ($validViewModes as $typeUid => $viewModes) {
            if (!isset($this->viewModes[$typeUid])) {
                $this->addError('viewMode-'.$typeUid, \Craft::t('themes', 'View mode is required')); 
            } elseif (!in_array($this->viewModes[$typeUid], array_keys($viewModes))) {
                $this->addError('viewMode-'.$typeUid, \Craft::t('themes', 'View mode is invalid'));
            }
        }
    }
}