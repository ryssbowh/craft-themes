<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\traits\DateOptions;

class DateTimeOptions extends FieldDisplayerOptions
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
            'format' => 'dd/LL/y kk:mm:ss',
            'custom' => '',
        ];
    }

    /**
     * Get available date icu formats
     * 
     * @return array
     */
    public function getFormats(): array
    {
        return [
            'dd/LL/y kk:mm:ss',
            'dd/LL/y kk:mm',
            'dd/LL/y K:mm a',
            'LL/dd/y kk:mm:ss',
            'LL/dd/y kk:mm',
            'LL/dd/y K:mm a',
            'd LLLL y, kk:mm:ss',
            'd LLLL y, kk:mm',
            'd LLLL y, K:mm a',
            'LLLL d, y, kk:mm:ss',
            'LLLL d, y, kk:mm',
            'LLLL d, y, K:mm a'
        ];
    }
}