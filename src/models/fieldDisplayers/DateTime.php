<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DateTimeOptions;
use Ryssbowh\CraftThemes\models\fields\DateUpdated;
use Ryssbowh\CraftThemes\models\fields\PostDate;
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
    public static $handle = 'datetime';

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
    public static function getFieldTargets(): array
    {
        return [Date::class, PostDate::class, DateUpdated::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return DateTimeOptions::class;
    }
}