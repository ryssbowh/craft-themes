<?php
namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Field as BaseField;

/**
 * Class that handles most Craft fields (all of them apart from Matrix and Table)
 */
interface CraftFieldInterface extends FieldInterface
{
    /**
     * Get the associated craft field instance
     * 
     * @return BaseField
     */
    public function getCraftField(): BaseField;

    /**
     * Create field from a craft field
     * 
     * @param  BaseField $craftField
     * @return FieldInterface
     */
    public static function createFromField(BaseField $craftField): FieldInterface;

    /**
     * Callback when the associated Craft field is changed
     * 
     * @param  BaseField $field
     * @return bool should the associated display be resaved
     */
    public function onCraftFieldChanged(BaseField $field): bool;
}