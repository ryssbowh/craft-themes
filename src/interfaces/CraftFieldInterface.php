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
}