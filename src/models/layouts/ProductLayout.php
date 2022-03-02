<?php
namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\traits\ElementLayout;
use craft\commerce\Plugin as Commerce;
use craft\models\FieldLayout;

/**
 * A layout associated to a volume and a theme
 */
class ProductLayout extends Layout
{
    use ElementLayout;

    /**
     * @var string
     */
    protected $_type = LayoutService::PRODUCT_HANDLE;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['elementUid', 'required'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Product : {name}', ['name' => $this->element->name]);
    }

    /**
     * @inheritDoc
     */
    public function getFieldLayout(): ?FieldLayout
    {
        return $this->element->getProductFieldLayout();
    }

    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        return Commerce::getInstance()->productTypes->getProductTypeByUid($this->elementUid);
    }
}