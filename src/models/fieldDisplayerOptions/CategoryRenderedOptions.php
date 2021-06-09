<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

class CategoryRenderedOptions extends FieldDisplayerOptions
{
    /**
     * @var string
     */
    public $viewMode;

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