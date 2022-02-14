<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\traits\SlickOptions;

class TagSlickOptions extends TagRenderedOptions
{
    use SlickOptions;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), $this->defineSlickRules());
    }

    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return array_merge(parent::defineOptions(), $this->defineSlickOptions());
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return array_merge(parent::defineDefaultValues(), $this->defineDefaultSlickValues());
    }
}