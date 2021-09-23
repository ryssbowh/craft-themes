<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class ViewModeOptions extends FieldDisplayerOptions
{
    /**
     * @var string
     */
    protected $_viewMode;
    
    /**
     * View mode getter
     * 
     * @return string
     */
    public function getViewMode()
    {
        if ($this->_viewMode === null) {
            $keys = array_keys($this->displayer->getViewModes());
            $this->_viewMode = $keys[0];
        }
        return $this->_viewMode;
    }

    /**
     * View mode setter
     * 
     * @param string $viewMode uid
     */
    public function setViewMode(string $viewMode)
    {
        $this->_viewMode = $viewMode;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['viewMode']);
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['viewMode', 'validateViewMode', 'skipOnEmpty' => false]
        ];
    }

    /**
     * Validate view mode
     */
    public function validateViewMode()
    {
        if (!isset($this->displayer->getViewModes()[$this->viewMode])) {
            $this->addError('viewMode', \Craft::t('themes', 'View mode is invalid'));
        }
    }
}