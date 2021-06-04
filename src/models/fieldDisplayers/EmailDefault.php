<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EmailDefaultOptions;
use craft\base\Model;
use craft\fields\Email;

class EmailDefault extends FieldDisplayer
{
    public static $handle = 'email_default';

    public $hasOptions = true;

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return Email::class;
    }

    public function getOptionsModel(): Model
    {
        return new EmailDefaultOptions;
    }
}