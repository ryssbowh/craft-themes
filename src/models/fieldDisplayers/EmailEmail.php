<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EmailEmailOptions;
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
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

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
    public static function getFieldTargets(): array
    {
        return [Email::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return EmailEmailOptions::class;
    }
}