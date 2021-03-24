<?php 

namespace Ryssbowh\CraftThemes\models\displayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class CategoryRenderedOptions extends FieldDisplayerOptions
{
    public $viewMode;

    public function getTheme()
    {
        return $this->_field->layout()->theme;
    }

    public function getViewModes(): array
    {
        $source = $this->_field->craftField()->source;
        $elems = explode(':', $source);
        $group = \Craft::$app->categories->getGroupByUid($elems[1]);
        $layout = Themes::$plugin->layouts->get($this->getTheme(), $group->uid);
        $viewModes = [];
        foreach ($layout->getViewModes() as $viewMode) {
            $viewModes[$viewMode->handle] = $viewMode->name;
        }
        return $viewModes;
    }

    public function rules()
    {
        return [
            ['viewMode', 'validateViewMode']
        ];
    }

    public function validateViewMode()
    {
        if (!isset($this->getViewModes()[$this->viewMode])) {
            $this->addError('viewMode', \Craft::t('themes', 'View mode is invalid'));
        }
    }
}