<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AuthorDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Author;
use craft\base\Model;

class AuthorDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'author_default';

    /**
     * @inheritDoc
     */
    public $hasOptions = true;

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

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
    public static function getFieldTarget(): string
    {
        return Author::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new AuthorDefaultOptions;
    }
}