<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DateOptions;
use Ryssbowh\CraftThemes\models\fields\DateCreated;
use Ryssbowh\CraftThemes\models\fields\DateUpdated;
use Ryssbowh\CraftThemes\models\fields\PostDate;
use Ryssbowh\CraftThemes\models\fields\UserLastLoginDate;
use craft\fields\Date as DateField;

/**
 * Renders a date field
 */
class Date extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'date';

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
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [DateField::class, PostDate::class, DateUpdated::class, DateCreated::class, UserLastLoginDate::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return DateOptions::class;
    }
}