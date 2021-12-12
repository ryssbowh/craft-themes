<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\MultiSelectLabelOptions;
use craft\base\Model;
use craft\fields\MultiSelect;

/**
 * Renders a multiselect field
 */
class MultiSelectLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'multiselect_label';

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
        return [MultiSelect::class];
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        $selected = array_filter($value->getOptions(), function ($option) {
            return $option->selected;
        });
        return !(empty($selected) and Themes::$plugin->settings->hideEmptyFields);
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return MultiSelectLabelOptions::class;
    }
}