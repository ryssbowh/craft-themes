<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UserDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Author;
use craft\base\Model;

/**
 * Renders the author of an entry
 */
class AuthorDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'author_default';

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
        return [Author::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return UserDefaultOptions::class;
    }
}