<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UserDefaultOptions;
use craft\base\Model;
use craft\fields\Users;

/**
 * Renders a user field
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
        return [Users::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return UserDefaultOptions::class;
    }
}