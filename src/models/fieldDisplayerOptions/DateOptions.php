<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\traits\DateOptions as DateOptionsTrait;

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
            'format' => 'd/m/Y',
            'custom' => '',
        ];
    }

    /**
     * Get available formats
     * 
     * @return array
     */
    public function getFormats(): array
    {
        return [
            'd/m/Y' => \Craft::t('themes', '31/10/2005'),
            'm/d/Y' => \Craft::t('themes', '10/31/2005'),
            'jS F Y' => \Craft::t('themes', '31st October 2005'),
            'F j, Y' => \Craft::t('themes', 'October 31, 2005'),
            'j F Y' => \Craft::t('themes', '31 October 2005'),
            'custom' => \Craft::t('themes', 'Custom'),
        ];
    }
}