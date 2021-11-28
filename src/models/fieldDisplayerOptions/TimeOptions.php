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
            'format' => 'H:i:s',
            'custom' => '',
        ];
    }

    /**
     * Get available date formats
     * 
     * @return array
     */
    public function getFormats(): array
    {
        return [
            'H:i:s' => \Craft::t('themes', '13:25:13'),
            'H:i' => \Craft::t('themes', '13:25'),
            'g:ia' => \Craft::t('themes', '1:25pm'),
            'custom' => \Craft::t('themes', 'Custom'),
        ];
    }
}