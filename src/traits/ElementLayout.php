<?php
namespace Ryssbowh\CraftThemes\traits;

use craft\db\Query;
use craft\db\Table;
use craft\models\FieldLayout;

/**
 * Common methods for layouts that are attached to elements (not customs or defaults layouts)
 */
trait ElementLayout
{
    /**
     * @var FieldLayout
     */
    protected $_fieldLayout;

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
        return $this->fieldLayout->getCustomFields();
    }

    /**
     * Get layout's element field layout.
     * We create the layout again here due to an issue in Craft where layout fields aren't properly propagated
     *
     * @see    https://github.com/craftcms/cms/issues/10237
     * @return FieldLayout
     */
    public function getFieldLayout(): ?FieldLayout
    {
        if ($this->_fieldLayout === null) {
            $idAttribute = $this->fieldLayoutIdAttribute;
            $id = $this->element->$idAttribute;
            $result = (new Query)->select([
                'id',
                'type',
                'uid',
            ])
            ->from([Table::FIELDLAYOUTS])
            ->where(['dateDeleted' => null])
            ->andWhere(['id' => $id])
            ->one();
            $this->_fieldLayout = new FieldLayout($result ?? []);
        }
        return $this->_fieldLayout;
    }
}