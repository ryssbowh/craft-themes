<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UserDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Author;
use craft\fields\Users;

/**
 * Renders the author of an entry
 */
class UserDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'user-default';

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
        return [Author::class, Users::class];
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        $eagerLoad[] = $prefix . 'photo';
        return $eagerLoad;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        if ($this->field instanceof Author and !empty($value)) {
            $value = [$value];
        }
        return parent::beforeRender($value);
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return UserDefaultOptions::class;
    }
}