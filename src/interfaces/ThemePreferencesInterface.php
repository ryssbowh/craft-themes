<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\elements\Asset;

/**
 * Each theme define some preferences which control the classes and attributes
 * of the page elements when they are rendered.
 * Those elements are : Layout, Block, Region, File, Field and Groups
 */
interface ThemePreferencesInterface 
{
    /**
     * Layout classes getter
     * 
     * @param  LayoutInterface   $layout
     * @param  boolean           $root true if we're rendering a page template
     * @return array
     */
    public function getLayoutClasses(LayoutInterface $layout, bool $root = false): array;

    /**
     * Get layout attributes
     * 
     * @param  LayoutInterface   $layout
     * @param  boolean           $root true if we're rendering a page template
     * @return array
     */
    public function getLayoutAttributes(LayoutInterface $layout, bool $root = false): array;

    /**
     * Get block classes
     * 
     * @param  BlockInterface $block
     * @return array
     */
    public function getBlockClasses(BlockInterface $block): array;

    /**
     * Get block attributes
     * 
     * @param  BlockInterface $block
     * @return array
     */
    public function getBlockAttributes(BlockInterface $block): array;

    /**
     * Get region classes
     * 
     * @param  RegionInterface $region
     * @return array
     */
    public function getRegionClasses(RegionInterface $region): array;

    /**
     * Get region attributes
     * 
     * @param  RegionInterface $region
     * @return array
     */
    public function getRegionAttributes(RegionInterface $region): array;

    /**
     * Get field classes
     * 
     * @param  FieldInterface $field
     * @return array
     */
    public function getFieldClasses(FieldInterface $field): array;

    /**
     * Get field attributes
     * 
     * @param  FieldInterface $field
     * @return array
     */
    public function getFieldAttributes(FieldInterface $field): array;

    /**
     * Get field container classes
     * 
     * @param  FieldInterface $field
     * @return array
     */
    public function getFieldContainerClasses(FieldInterface $field): array;

    /**
     * Get field container attributes
     * 
     * @param  FieldInterface $field
     * @return array
     */
    public function getFieldContainerAttributes(FieldInterface $field): array;

    /**
     * Get field label classes
     * 
     * @param  FieldInterface $field
     * @return array
     */
    public function getFieldLabelClasses(FieldInterface $field): array;

    /**
     * Get field label attributes
     * 
     * @param  FieldInterface $field
     * @return array
     */
    public function getFieldLabelAttributes(FieldInterface $field): array;

    /**
     * Get group classes
     * 
     * @param  GroupInterface $group
     * @return array
     */
    public function getGroupClasses(GroupInterface $group): array;

    /**
     * Get group attributes
     * 
     * @param  GroupInterface $group
     * @return array
     */
    public function getGroupAttributes(GroupInterface $group): array;

    /**
     * Get group container classes
     * 
     * @param  GroupInterface $group
     * @return array
     */
    public function getGroupContainerClasses(GroupInterface $group): array;

    /**
     * Get group container attributes
     * 
     * @param  GroupInterface $group
     * @return array
     */
    public function getGroupContainerAttributes(GroupInterface $group): array;

    /**
     * Get group label classes
     * 
     * @param  GroupInterface $group
     * @return array
     */
    public function getGroupLabelClasses(GroupInterface $group): array;

    /**
     * Get group container classes
     * 
     * @param  GroupInterface $group
     * @return array
     */
    public function getGroupLabelAttributes(GroupInterface $group): array;

    /**
     * Get file classes
     * 
     * @param  Asset                  $asset
     * @param  FieldInterface         $field
     * @param  FileDisplayerInterface $displayer
     * @return array
     */
    public function getFileClasses(Asset $asset, FieldInterface $field, FileDisplayerInterface $displayer): array;

    /**
     * Get file attributes
     * 
     * @param  Asset                  $asset
     * @param  FieldInterface         $field
     * @param  FileDisplayerInterface $displayer
     * @return array
     */
    public function getFileAttributes(Asset $asset, FieldInterface $field, FileDisplayerInterface $displayer): array;
}