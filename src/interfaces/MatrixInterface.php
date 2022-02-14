<?php
namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Field as BaseField;
use craft\elements\MatrixBlock;

/**
 * A matrix is a type of field, it handles the Craft matrix fields
 */
interface MatrixInterface extends FieldInterface
{   
    /**
     * Types setter
     * 
     * @param DisplayMatrixType[] $types
     */
    public function setTypes(array $types);

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
     * @return FieldInterface[]
     */
    public function getVisibleFields(MatrixBlock $block): array;

    /**
     * Get a field by Craft field handle
     * 
     * @param  MatrixBlock $block
     * @param  string      $handle
     * @return ?FieldInterface
     */
    public function getFieldByHandle(MatrixBlock $block, string $handle): ?FieldInterface;

    /**
     * Get fields by handles
     * 
     * @param  MatrixBlock $block
     * @param  string[]    $handles
     * @return FieldInterface[]
     */
    public function getFieldsByHandles(MatrixBlock $block, array $handles): array;

    /**
     * Get a field by uid
     * 
     * @param  MatrixBlock $block
     * @param  string      $uid
     * @return FieldInterface
     */
    public function getFieldByUid(MatrixBlock $block, string $uid): ?FieldInterface;

    /**
     * Get fields by uids
     * 
     * @param  MatrixBlock $block
     * @param  string[]    $uids
     * @return FieldInterface[]
     */
    public function getFieldsByUids(MatrixBlock $block, array $uids): array;
}