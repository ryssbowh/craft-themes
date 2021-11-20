<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class ViewModeOptions extends FieldDisplayerOptions
{
    /**
     * @var string
     */
    protected $_viewModeUid;
    
    /**
     * View mode getter
     * 
     * @return string
     */
    public function getViewModeUid()
    {
        if ($this->_viewModeUid === null) {
            $keys = array_keys($this->displayer->getViewModes());
            $this->_viewModeUid = $keys[0] ?? null;
        }
        return $this->_viewModeUid;
    }

    /**
     * View mode setter
     * 
     * @param null|string $viewModeUid
     */
    public function setViewModeUid(?string $viewModeUid)
    {
        $this->_viewModeUid = $viewModeUid;
    }

    /**
     * Get the view mode
     * 
     * @return ?ViewModeInterface
     */
    public function getViewMode(): ?ViewModeInterface
    {
        if ($this->_viewModeUid) {
            return Themes::$plugin->viewModes->getByUid($this->_viewModeUid);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['viewModeUid']);
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['viewModeUid', 'validateViewMode', 'skipOnEmpty' => false]
        ];
    }

    /**
     * Validate view mode
     */
    public function validateViewMode()
    {
        if (!isset($this->displayer->getViewModes()[$this->_viewModeUid])) {
            $this->addError('viewModeUid', \Craft::t('themes', 'View mode is invalid'));
        }
    }
}