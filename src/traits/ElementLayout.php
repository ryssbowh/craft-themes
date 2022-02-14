<?php
namespace Ryssbowh\CraftThemes\traits;

use craft\models\FieldLayout;

/**
 * Common methods for layouts that are attached to elements (not customs or defaults layouts)
 */
trait ElementLayout
{
    /**
     * @inheritDoc
     */
    public function getTemplatingKey(): string
    {
        return $this->element->handle;
    }

    /**
     * @inheritDoc
     */
    public function hasDisplays(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCraftFields(): array
    {
        return $this->fieldLayout->getFields();
    }

    /**
     * @inheritDoc
     */
    public function getFieldLayout(): ?FieldLayout
    {
        return $this->element->getFieldLayout();
    }
}