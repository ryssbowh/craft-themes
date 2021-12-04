<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\helpers\ViewModesHelper;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\traits\ViewModeOptions;

class TagRenderedOptions extends FieldDisplayerOptions
{
    use ViewModeOptions;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return $this->defineViewModeRules();
    }

    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return $this->defineViewModeOptions();
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return $this->defineViewModeDefaultValues();
    }

    /**
     * Get view modes available, based on this displayer's field tag
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        return ViewModesHelper::getTagGroupViewModes($this->displayer->field->craftField, $this->displayer->getTheme());
    }
}