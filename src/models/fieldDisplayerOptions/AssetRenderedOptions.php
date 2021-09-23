<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Asset;

class AssetRenderedOptions extends FieldDisplayerOptions
{
    /**
     * @var array
     */
    protected $_viewModes;

    /**
     * Get all view modes
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        if (is_null($this->_viewModes)) {
            $this->_viewModes = [];
            foreach ($this->displayer->getViewModes() as $volumeUid => $viewModes) {
                $keys = array_keys($viewModes['viewModes']);
                $this->_viewModes[$volumeUid] = $keys[0];
            }
        }
        return $this->_viewModes;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['viewModes']);
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['viewModes', 'validateViewModes', 'skipOnEmpty' => false]
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

    public function getViewMode(Asset $asset): ?ViewModeInterface
    {
        $volume = $asset->volume;
        $viewModeUid = $this->viewModes[$volume->uid] ?? null;
        return $viewModeUid ? Themes::$plugin->viewModes->getByUid($viewModeUid) : null;
    }
}