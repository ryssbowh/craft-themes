<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Asset;

class AssetRenderedOptions extends FieldDisplayerOptions
{
    /**
     * @var array
     */
    public $viewModes;

    /**
     * @inheritDoc
     */
    public function init()
    {
        if ($this->viewModes === null) {
            $this->viewModes = [];
            foreach ($this->displayer->getViewModes() as $volumeUid => $viewModes) {
                $keys = array_keys($viewModes['viewModes']);
                $this->viewModes[$volumeUid] = $keys[0];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['viewModes', 'validateViewModes']
        ];
    }

    /**
     * Validate view modes
     */
    public function validateViewModes()
    {
        $validViewModes = $this->displayer->getViewModes();
        foreach ($this->viewModes as $volumeUid => $viewModeUid) {
            if (!isset($validViewModes[$volumeUid]['viewModes'][$viewModeUid])) {
               $this->addError('viewMode-'.$volumeUid, \Craft::t('themes', 'View mode is invalid'));
            }
        }
    }

    public function getViewMode(Asset $asset): ?ViewMode
    {
        $volume = $asset->volume;
        $viewModeUid = $this->viewModes[$volume->uid] ?? null;
        return $viewModeUid ? Themes::$plugin->viewModes->getByUid($viewModeUid) : null;
    }
}