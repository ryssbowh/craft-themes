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
     * Get associated matrix field instance, used for fields that are part of a matrix
     * 
     * @return ?CraftField
     */
    public function getMatrix(): ?CraftField;
}