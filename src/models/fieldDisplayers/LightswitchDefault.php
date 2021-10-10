<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\base\Model;
use craft\fields\Lightswitch;

/**
 * Renders a lightswitch field
 */
class LightswitchDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'lightswitch_default';

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
        return Lightswitch::class;
    }
}