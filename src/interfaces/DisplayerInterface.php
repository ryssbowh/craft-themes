<?php
namespace Ryssbowh\CraftThemes\interfaces;

/**
 * Base interface for displayers
 */
interface DisplayerInterface
{
    /**
     * Get name
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Get handle
     * 
     * @return string
     */
    public static function getHandle(): string;

    /**
     * Description/helper shown in CP
     * 
     * @return string
     */
    public function getDescription(): string;

    /**
     * Does this displayer define options
     * 
     * @return bool
     */
    public function getHasOptions(): bool;

    /**
     * Can this displayer be cached
     * 
     * @return bool
     */
    public function getCanBeCached(): bool;

    /**
     * Field getter
     * 
     * @return FieldInterface
     */
    public function getField(): FieldInterface;

    /**
     * Modify eager load map
     *
     * @param  string[] $eagerLoad
     * @param  string   $prefix
     * @param  int      $level
     * @return string[]
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array;
}