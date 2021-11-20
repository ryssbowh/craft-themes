<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EmailEmailOptions;
use craft\base\Model;
use craft\fields\Email;

/**
 * Renders an email field
 */
class EmailEmail extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'email_email';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Email');
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
        return EmailEmailOptions::class;
    }
}