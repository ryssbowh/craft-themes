<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EmailDefaultOptions;
use craft\base\Model;
use craft\fields\Email;

class EmailDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'email_default';

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
        return Email::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return EmailDefaultOptions::class;
    }
}