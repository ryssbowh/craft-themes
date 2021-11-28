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
            'format' => 'd/m/Y H:i:s',
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
            'd/m/Y H:i:s' => \Craft::t('themes', '31/10/2005 13:25:13'),
            'm/d/Y H:i:s' => \Craft::t('themes', '10/31/2005 13:25:13'),
            'jS F Y, H:i:s' => \Craft::t('themes', '31st October 2005, 13:25:13'),
            'F j, Y, H:i:s' => \Craft::t('themes', 'October 31, 2005, 13:25:13'),
            'j F Y, H:i:s' => \Craft::t('themes', '31 October 2005, 13:25:13'),
            'd/m/Y H:i' => \Craft::t('themes', '31/10/2005 13:25'),
            'm/d/Y H:i' => \Craft::t('themes', '10/31/2005 13:25'),
            'jS F Y, H:i' => \Craft::t('themes', '31st October 2005, 13:25'),
            'F j, Y, H:i' => \Craft::t('themes', 'October 31, 2005, 13:25'),
            'custom' => \Craft::t('themes', 'Custom'),
        ];
    }
}