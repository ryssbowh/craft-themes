<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\traits\DateOptions;

class TimeOptions extends FieldDisplayerOptions
{
    use DateOptions;
    
    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), $this->defineDateRules());
    }

    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return array_merge(parent::defineOptions(), $this->defineDateOptions());
    }

    /**
     * @inheritDoc
     */
    public function defineDefaultValues(): array
    {
        return [
            'format' => 'kk:mm:ss',
            'custom' => '',
        ];
    }

    /**
     * Get available time icu formats
     * 
     * @return array
     */
    public function getFormats(): array
    {
        return [
            'kk:mm:ss',
            'kk:mm',
            'K:mm a'
        ];
    }
}