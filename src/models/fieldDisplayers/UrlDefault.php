<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UrlDefaultOptions;
use craft\base\Model;
use craft\fields\Url;

class UrlDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'url_default';

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
    public static function getFieldTarget(): String
    {
        return Url::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return UrlDefaultOptions::class;
    }
}