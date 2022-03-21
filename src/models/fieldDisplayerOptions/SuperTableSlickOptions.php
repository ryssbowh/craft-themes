<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\traits\SlickOptions;

class SuperTableSlickOptions extends FieldDisplayerOptions
{
    use SlickOptions;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return $this->defineSlickRules();
    }

    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return $this->defineSlickOptions();
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return $this->defineDefaultSlickValues();
    }
}