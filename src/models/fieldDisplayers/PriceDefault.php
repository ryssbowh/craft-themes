<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\PriceDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Price;
use craft\commerce\elements\Product;
use craft\fields\Email;

/**
 * Renders a variant price
 */
class PriceDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'price-default';

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
        return \Craft::t('themes', 'Default');
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        if ($this->options->display) {
            $element = Themes::$plugin->view->renderingElement;
            if ($element instanceof Product) {
                $value = $element->variants[0]->salePrice;
            } else {
                $value = $element->salePrice;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Price::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return PriceDefaultOptions::class;
    }
}