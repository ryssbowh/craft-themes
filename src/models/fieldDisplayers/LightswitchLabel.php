<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\LightswitchLabelOptions;
use craft\fields\Lightswitch;

/**
 * Renders a lightswitch field
 */
class LightswitchLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'lightswitch-label';

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
        return \Craft::t('app', 'Label');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Lightswitch::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return LightswitchLabelOptions::class;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        return !(is_null($value) and Themes::$plugin->settings->hideEmptyFields);
    }
}