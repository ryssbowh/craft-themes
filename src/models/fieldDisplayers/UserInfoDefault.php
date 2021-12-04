<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UserDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\UserInfo;
use craft\base\Model;

/**
 * Renders a user info field
 */
class UserInfoDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'user-info_default';

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
    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [UserInfo::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return UserDefaultOptions::class;
    }
}