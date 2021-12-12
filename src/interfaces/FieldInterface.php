<?php
namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Element;
use craft\base\Field as BaseField;

/**
 * A field is a type of item, it can handle a Craft field or a theme field (title, author etc)
 */
interface FieldInterface
{   
    /**
     * The class used by displayer for their field targets
     * Will be the field class for custom fields
     * and the craft field class for craft fields
     * 
     * @return string
     */
    public function getTargetClass(): string;

    /**
     * For which craft field class this field should be used
     * 
     * @return string
     */
    public static function forField(): string;

    /**
     * Create a new field from config
     * 
     * @param  array  $config
     * @return FieldInterface
     */
    public static function create(?array $config = null): FieldInterface;

    /**
     * Saves a field
     * 
     * @param  array  $uid
     * @param  array  $data
     * @return bool
     */
    public static function save(FieldInterface $field): bool;

    /**
     * Handle a project config change
     * 
     * @param  string $uid
     * @param  array  $data
     * @return bool
     */
    public static function handleChanged(string $uid, array $data);

    /**
     * Delete a field
     * 
     * @param  FieldInterface $field
     * @return bool
     */
    public static function delete(FieldInterface $field): bool;

    /**
     * Deletes a field record
     * 
     * @param array  $uid
     * @param array  $data
     */
    public static function handleDeleted(string $uid, array $data);

    /**
     * Populate this field from post data
     * 
     * @param array $data
     */
    public function populateFromPost(array $data);

    /**
     * Should this field exist on a layout, called during the creation of a layout
     * to automatically create this field on that layout.
     * 
     * @param  LayoutInterface $layout
     * @return bool
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool;

    /**
     * Options getter
     * 
     * @return array
     */
    public function getOptions(): array;

    /**
     * Options setter
     * 
     * @param ?array $options
     */
    public function setOptions(?array $options);

    /**
     * Get the displayer for this field
     * 
     * @return FieldDisplayerInterface
     */
    public function getDisplayer(): ?FieldDisplayerInterface;

    /**
     * Displayer handle getter
     * 
     * @return string
     */
    public function getDisplayerHandle(): string;

    /**
     * Displayer handle setter
     * 
     * @param string $handle
     */
    public function setDisplayerHandle(string $handle);

    /**
     * Get all the displayers that can display this field
     * 
     * @return FieldDisplayerInterface[]
     */
    public function getAvailableDisplayers(): array;

    /**
     * Name to display
     * 
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Get available field templates
     * 
     * @return string[]
     */
    public function getFieldTemplates(): array;

    /**
     * Get available file templates
     * 
     * @param  FileDisplayerInterface $displayer
     * @return string[]
     */
    public function getFileTemplates(FileDisplayerInterface $displayer): array;

    /**
     * Render this item.
     * Custom field must override this to pull the correct value from 
     * the element being rendered which can be pulled from the View service.
     *
     * @param  mixed $value
     * @return string
     */
    public function render($value = null): string;

    /**
     * Can this field be cached
     * 
     * @return bool
     */
    public function getCanBeCached(): bool;
}