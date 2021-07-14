<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\fields\CraftField;
use craft\base\Field as BaseField;

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
     * @param  CraftField $field
     * @return bool should the layout be saved
     */
    public function onCraftFieldChanged(BaseField $field): bool;
}