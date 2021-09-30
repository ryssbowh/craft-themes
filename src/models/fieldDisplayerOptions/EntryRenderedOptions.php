<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Entry;

class EntryRenderedOptions extends FieldDisplayerOptions
{
    /**
     * @var array
     */
    protected $_viewModes;

    /**
     * @inheritDoc
     */
    public function getViewModes()
    {
        if ($this->_viewModes === null) {
            $this->_viewModes = [];
            foreach ($this->displayer->getViewModes() as $typeUid => $viewModes) {
                $keys = array_keys($viewModes['viewModes']);
                $this->_viewModes[$typeUid] = $keys[0];
            }
        }
        return $this->_viewModes;
    }

    /**
     * View modes setter
     * 
     * @param array $viewModes
     */
    public function setViewModes(array $viewModes)
    {
        $this->_viewModes = $viewModes;
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
     * validate view modes
     */
    public function validateViewModes()
    {
        $validViewModes = $this->displayer->getViewModes();
        foreach ($validViewModes as $typeUid => $viewModes) {
            if (!isset($this->viewModes[$typeUid])) {
                $this->addError('viewMode-'.$typeUid, \Craft::t('themes', 'View mode is required')); 
            } elseif (!in_array($this->viewModes[$typeUid], array_keys($viewModes['viewModes'] ?? []))) {
                $this->addError('viewMode-'.$typeUid, \Craft::t('themes', 'View mode is invalid'));
            }
        }
    }
}