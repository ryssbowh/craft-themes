<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TitleDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Title;
use craft\base\Model;

class TitleDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'title_default';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public $hasOptions = true;

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
    public static function getFieldTarget(): String
    {
        return Title::class;
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
    public function getOptionsModel(): Model
    {
        return new TitleDefaultOptions;
    }
}