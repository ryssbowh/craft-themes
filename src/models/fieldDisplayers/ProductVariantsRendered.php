<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\ViewModesHelper;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\VariantsRenderedOptions;
use Ryssbowh\CraftThemes\models\fields\Variants;
use craft\commerce\elements\Variant;

/**
 * Renders the variants of a product
 */
class ProductVariantsRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'product-variants-rendered';

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
        return \Craft::t('themes', 'Rendered');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Variants::class];
    }

    /**
     * Get view modes available, based on the field users
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        $type = $this->field->layout->element;
        $viewModes = ViewModesHelper::getVariantViewModes($this->getTheme(), $type);
        return $viewModes[$type->uid]['viewModes'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        foreach ($this->getViewModes() as $uid => $label) {
            $viewMode = Themes::$plugin->viewModes->getByUid($uid);
            //Avoid infinite loops for self referencing view modes :
            if ($viewMode->id != $this->field->viewMode->id) {
                $eagerLoad = array_merge($eagerLoad, $viewMode->eagerLoad($prefix, $level));
            }
        }
        return $eagerLoad;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return VariantsRenderedOptions::class;
    }
}