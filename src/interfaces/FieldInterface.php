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
     * Saves a field from an array of data
     * 
     * @param  array  $uid
     * @param  array  $data
     * @return bool
     */
    public static function save(string $uid, array $data): bool;

    /**
     * Deletes a field record
     * 
     * @param array  $uid
     * @param array  $data
     */
    public static function delete(string $uid, array $data);

    /**
     * Populate this field from post data
     * 
     * @param array  $data
     */
    public function populateFromPost(array $data);

    /**
     * Should this field exist on a layout, called during the creation of a layout
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
     * @param array $options
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
     * Get available field templates
     * 
     * @param  LayoutInterface         $layout
     * @param  ViewModeInterface       $viewMode
     * @param  FieldDisplayerInterface $displayer
     * @return array
     */
    public function getFieldTemplates(LayoutInterface $layout, ViewModeInterface $viewMode, FieldDisplayerInterface $displayer): array;

    /**
     * Get available file templates
     * 
     * @param  LayoutInterface         $layout
     * @param  ViewModeInterface       $viewMode
     * @param  FileDisplayerInterface $displayer
     * @return array
     */
    public function getFileTemplates(LayoutInterface $layout, ViewModeInterface $viewMode, FileDisplayerInterface $displayer): array;

    /**
     * Render this item.
     *
     * @param  mixed $value
     * @return string
     */
    public function render($value = null): string;
}