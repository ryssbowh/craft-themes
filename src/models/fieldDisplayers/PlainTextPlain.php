<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\PlainTextFullOptions;
use Ryssbowh\CraftThemes\models\fields\Sku;
use Ryssbowh\CraftThemes\models\fields\UserFirstName;
use Ryssbowh\CraftThemes\models\fields\UserLastName;
use Ryssbowh\CraftThemes\models\fields\UserUsername;
use craft\fields\PlainText;

/**
 * Renders a plain text field
 */
class PlainTextPlain extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'plaintext-plain';

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
        return \Craft::t('themes', 'Plain');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [PlainText::class, UserFirstName::class, UserLastName::class, UserUsername::class, Sku::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return PlainTextFullOptions::class;
    }
}