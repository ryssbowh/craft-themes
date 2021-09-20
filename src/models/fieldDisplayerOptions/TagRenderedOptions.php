<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class TagRenderedOptions extends FieldDisplayerOptions
{
    /**
     * @var string
     */
    protected $_viewMode;

    /**
     * @inheritDoc
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
            ['viewMode', 'validateViewMode']
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