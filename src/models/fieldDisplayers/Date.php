<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DateOptions;
use craft\base\Model;
use craft\fields\Date as DateField;

/**
 * Renders a date field
 */
class Date extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'date_date';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Date');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return DateField::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return DateOptions::class;
    }

    /**
     * Get available date formats
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

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['formats']);
    }
}