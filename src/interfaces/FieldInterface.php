<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Element;
use craft\base\Field as BaseField;

interface FieldInterface extends DisplayItemInterface
{
    /**
     * Type getter
     * 
     * @return string
     */
    public static function getType(): string;

    /**
     * Create a new field from config
     * 
     * @param  array  $config
     * @return FieldInterface
     */
    public static function create(array $config): FieldInterface;

    /**
     * Saves a field from an array of data
     * 
     * @param  array  $data
     * @return bool
     */
    public static function save(array $data): bool;

    /**
     * Should this field exist on a layout, called during the creation of a layout
     * 
     * @param  LayoutInterface $layout
     * @return bool
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool;

    /**
     * Create a new field from a craft field instance
     * 
     * @param  BaseField|null $craftField
     * @return FieldInterface
     */
    public static function createNew(?BaseField $craftField = null): FieldInterface;

    /**
     * Build the config for a craft field
     * 
     * @param  BaseField $craftField
     * @return array
     */
    public static function buildConfig(?BaseField $craftField): array;

    /**
     * Is this field visible
     * 
     * @return boolean
     */
    public function isVisible(): bool;

    /**
     * Get the displayer for this field
     * 
     * @return FieldDisplayerInterface
     */
    public function getDisplayer(): ?FieldDisplayerInterface;

    /**
     * Get all the displayers that can display this field
     * 
     * @return array
     */
    public function getAvailableDisplayers(): array;

    /**
     * Name to display
     * 
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Render this field for an element
     * 
     * @param  Element $element
     * @return string
     */
    public function render(Element $element): string;

    /**
     * Handle getter
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Name getter
     * 
     * @return string
     */
    public function getName(): string;
}