<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\traits\DateOptions as DateOptionsTrait;
use craft\i18n\FormatConverter;

class DateOptions extends FieldDisplayerOptions
{
    use DateOptionsTrait;

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
            'format' => 'dd/LL/y',
            'custom' => '',
        ];
    }

    /**
     * Get available icu formats
     * 
     * @return array
     */
    protected function getFormats(): array
    {
        return [
            'dd/LL/y',
            'LL/dd/y',
            'd LLLL y',
            'LLLL d, y'
        ];
    }
}