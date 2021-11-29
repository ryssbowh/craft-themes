<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

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
}