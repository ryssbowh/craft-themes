<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Field as BaseField;
use craft\elements\MatrixBlock;

interface MatrixInterface extends FieldInterface
{   
    /**
     * Get matrix types, indexed by matrix block type handles
     * 
     * @return DisplayMatrixType[]
     */
    public function getTypes(): array;

    /**
     * Get all visible fields defined in a Matric block
     * 
     * @param  MatrixBlock $block
     * @return array
     */
    public function getVisibleFields(MatrixBlock $block): array;
}