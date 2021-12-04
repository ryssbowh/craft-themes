<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UserDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Author;
use Ryssbowh\CraftThemes\models\fields\UserInfo;
use craft\base\Model;
use craft\fields\Users;

/**
 * Renders the author of an entry
 */
class UserDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'user_default';

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
        return [Author::class, UserInfo::class, Users::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return UserDefaultOptions::class;
    }
}