<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DateTimeOptions;
use craft\base\Model;
use craft\fields\Date;

/**
 * Renders a date field
 */
class DateTime extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'date_datetime';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Date and time');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return Date::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return DateTimeOptions::class;
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

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['formats']);
    }
}