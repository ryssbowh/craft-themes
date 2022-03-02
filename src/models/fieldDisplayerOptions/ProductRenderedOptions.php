<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayerOptions;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;
use Ryssbowh\CraftThemes\traits\ViewModesOptions;
use craft\commerce\elements\Product;

class ProductRenderedOptions extends FieldDisplayerOptions
{
    use ViewModesOptions;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return $this->defineViewModesRules();
    }

    /**
     * @inheritDoc
     */
    public function defineOptions(): array
    {
        return $this->defineViewModesOptions();
    }

    /**
     * Get the view mode for a product
     * 
     * @param  Product $product
     * @return ?ViewModeInterface
     */
    public function getViewMode(Product $product): ?ViewModeInterface
    {
        $type = $product->type;
        if ($type) {
            $viewModeUid = $this->getValue('viewMode-' . $type->uid);
            if ($viewModeUid) {
                try {
                    return Themes::$plugin->viewModes->getByUid($viewModeUid);
                } catch (ViewModeException $e) {
                    return null;
                }
            }
        }
        return null;
    }
}