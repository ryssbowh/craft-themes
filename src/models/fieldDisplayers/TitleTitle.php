<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TitleTitleOptions;
use Ryssbowh\CraftThemes\models\fields\Title;
use craft\base\Model;

/**
 * Renders a title field
 */
class TitleTitle extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'title_title';

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
        return \Craft::t('app', 'Title');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Title::class];
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return TitleTitleOptions::class;
    }
}