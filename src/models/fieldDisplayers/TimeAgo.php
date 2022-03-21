<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TimeAgoOptions;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DateField;
use Ryssbowh\CraftThemes\models\fields\DateCreated;
use Ryssbowh\CraftThemes\models\fields\DateUpdated;
use Ryssbowh\CraftThemes\models\fields\PostDate;
use Ryssbowh\CraftThemes\models\fields\UserLastLoginDate;

/**
 * Renders a date field as time ago
 */
class TimeAgo extends Date
{
    /**
     * @inheritDoc
     */
    public static $handle = 'time-ago';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Time ago');
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
        return TimeAgoOptions::class;
    }
}