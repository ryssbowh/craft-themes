<?php
namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Field ;

/**
 * Class that handles most Craft fields (all of them apart from Matrix and Table)
 */
interface CraftFieldInterface extends FieldInterface
{
    /**
     * Get the associated craft field instance
     * 
     * @return Field
     */
    public function getCraftField(): Field;

    /**
     * Build config from a field
     * 
     * @param  Field $craftField
     * @return array
     */
    public static function buildConfig(Field $craftField): array;

    /**
     * Create field from a craft field
     * 
     * @param  Field $craftField
     * @return FieldInterface
     */
    public static function createFromField(Field $craftField): FieldInterface;
}