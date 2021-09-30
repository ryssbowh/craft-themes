<?php
namespace Ryssbowh\CraftThemes\helpers;

use craft\models\FieldLayout;

trait ElementLayoutTrait
{
    /**
     * @inheritDoc
     */
    public function getElementMachineName(): string
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