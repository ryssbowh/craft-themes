<?php

namespace Ryssbowh\CraftThemes\traits;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\behaviors\ProductTypeLayoutBehavior;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AllowedQtyDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DimensionsDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\PriceDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\ProductRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\ProductVariantsRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\StockDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\VariantRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\WeightDefault;
use Ryssbowh\CraftThemes\models\fields\AllowedQty;
use Ryssbowh\CraftThemes\models\fields\Dimensions;
use Ryssbowh\CraftThemes\models\fields\Price;
use Ryssbowh\CraftThemes\models\fields\Sku;
use Ryssbowh\CraftThemes\models\fields\Stock;
use Ryssbowh\CraftThemes\models\fields\Variants;
use Ryssbowh\CraftThemes\models\fields\Weight;
use Ryssbowh\CraftThemes\models\layouts\ProductLayout;
use Ryssbowh\CraftThemes\models\layouts\VariantLayout;
use Ryssbowh\CraftThemes\services\FieldDisplayerService;
use Ryssbowh\CraftThemes\services\FieldsService;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\commerce\Plugin as Commerce;
use craft\commerce\elements\Product;
use craft\commerce\models\ProductType;
use craft\commerce\services\ProductTypes;
use yii\base\Event;

/**
 * Integrates with commerce plugin
 * 
 * @since 3.1.0
 */
trait Ecommerce
{
    protected function initEcommerce()
    {
        if (\Craft::$app->plugins->isPluginEnabled('commerce')) {
            $this->_initEcommerce();
        }
    }

    protected function _initEcommerce()
    {
        Event::on(ProductTypes::class, ProductTypes::EVENT_AFTER_SAVE_PRODUCTTYPE, function (Event $e) {
            Themes::$plugin->layouts->onCraftElementSaved('product', $e->productType->uid);
        });
        \Craft::$app->projectConfig->onRemove(ProductTypes::CONFIG_PRODUCTTYPES_KEY.'.{uid}', function(Event $e) {
            if (\Craft::$app->getProjectConfig()->isApplyingExternalChanges) {
                // If Craft is applying Yaml changes it means we have the fields defined
                // in config, and don't need to respond to these events as it would create duplicates
                return;
            }
            Themes::$plugin->layouts->onCraftElementDeleted($e->tokenMatches[0]);
        });
        Event::on(FieldDisplayerService::class, FieldDisplayerService::EVENT_REGISTER_DISPLAYERS, function (Event $e) {
            $e->registerMany([
                AllowedQtyDefault::class,
                DimensionsDefault::class,
                PriceDefault::class,
                ProductRendered::class,
                ProductVariantsRendered::class,
                StockDefault::class,
                VariantRendered::class,
                WeightDefault::class
            ]);
        });
        Event::on(FieldsService::class, FieldsService::EVENT_REGISTER_FIELDS, function (Event $e) {
            $e->registerMany([
                Variants::class,
                Stock::class,
                Sku::class,
                Price::class,
                Dimensions::class,
                Weight::class,
                AllowedQty::class
            ]);
        });
        Event::on(LayoutService::class, LayoutService::EVENT_REGISTER_TYPES, function (Event $e) {
            $e->registerMany([
                'product' => ProductLayout::class,
                'variant' => VariantLayout::class
            ]);
        });
        Event::on(LayoutService::class, LayoutService::EVENT_AVAILABLE_LAYOUTS, function (Event $e) {
            $types = Commerce::getInstance()->productTypes->getAllProductTypes();
            foreach ($types as $type) {
                $productLayout = Themes::$plugin->layouts->create([
                    'type' => 'product',
                    'elementUid' => $type->uid,
                    'themeHandle' => $e->themeHandle
                ]);
                $variantLayout = Themes::$plugin->layouts->create([
                    'type' => 'variant',
                    'elementUid' => $type->uid,
                    'themeHandle' => $e->themeHandle,
                    'parent' => $productLayout
                ]);
                $e->layouts[] = $productLayout;
                $e->layouts[] = $variantLayout;
            }
        });
        Event::on(LayoutService::class, LayoutService::EVENT_RESOLVE_REQUEST_LAYOUT, function (Event $e) {
            if ($e->element instanceof Product) {
                $e->layout = Themes::$plugin->layouts->get($e->theme, 'product', $e->element->getType()->uid);
            }
        });
        // Add product type behavior, this won't have any effect before commerce 3.4.12
        // @see https://github.com/craftcms/commerce/issues/2715
        Event::on(ProductType::class, ProductType::EVENT_DEFINE_BEHAVIORS, function(Event $e) {
            $e->sender->attachBehaviors([
                'themeLayout' => [
                    'class' => ProductTypeLayoutBehavior::class
                ]
            ]);
        });
        \Craft::$app->view->hook('cp.commerce.product.edit.details', function (array &$context) {
            return \Craft::$app->view->renderTemplate('themes/cp/product-shortcuts', [
                'element' => $context['product']->type
            ]);
        });
    }
}