<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\UrlDefaultOptions;
use craft\base\Model;
use craft\fields\Url;

class UrlDefault extends FieldDisplayer
{
    public static $handle = 'url_default';

    public static $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return Url::class;
    }

    public function getOptionsModel(): Model
    {
        return new UrlDefaultOptions;
    }
}