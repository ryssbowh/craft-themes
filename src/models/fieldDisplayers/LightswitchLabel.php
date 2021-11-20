<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\base\Model;
use craft\fields\Lightswitch;

/**
 * Renders a lightswitch field
 */
class LightswitchLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'lightswitch_label';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Label');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return Lightswitch::class;
    }
}