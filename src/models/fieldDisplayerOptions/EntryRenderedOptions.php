<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

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
            } elseif (!in_array($this->viewModes[$typeUid], array_keys($viewModes))) {
                $this->addError('viewMode-'.$typeUid, \Craft::t('themes', 'View mode is invalid'));
            }
        }
    }
}