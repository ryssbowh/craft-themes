<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\ViewModesHelper;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\ProductRenderedOptions;
use craft\commerce\fields\Products;

/**
 * Renders a product field as rendered using a view mode
 */
class ProductRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'product-rendered';

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
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        foreach ($this->getViewModes() as $uid => $array) {
            foreach ($array['viewModes'] as $uid => $label) {
                $viewMode = Themes::$plugin->viewModes->getByUid($uid);
                //Avoid infinite loops for self referencing view modes :
                if ($viewMode->id != $this->field->viewMode->id) {
                    $eagerLoad = array_merge($eagerLoad, $viewMode->eagerLoad($prefix, $level));
                }
            }
        }
        return $eagerLoad;
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Products::class];
    }

    /**
     * Get view modes available
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        return ViewModesHelper::getProductsViewModes($this->field->craftField, $this->getTheme());
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return ProductRenderedOptions::class;
    }
}