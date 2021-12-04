<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TimeOptions;
use Ryssbowh\CraftThemes\models\fields\DateUpdated;
use Ryssbowh\CraftThemes\models\fields\PostDate;
use craft\base\Model;
use craft\fields\Time as TimeField;

/**
 * Renders a time field
 */
class Time extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'time';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return $fieldClass == TimeField::class;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Time');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [TimeField::class, PostDate::class, DateUpdated::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return TimeOptions::class;
    }
}