<?php 

namespace Ryssbowh\CraftThemes\helpers;

class ClassBag
{
    /**
     * @var array
     */
    protected $classes = [];

    /**
     * @inheritDoc
     */
    public function __construct(array $classes = [])
    {
        $this->add($classes);
    }

    /**
     * Add a class or several classes
     * 
     * @param  string|array $class
     * @return ClassBag
     */
    public function add($class): ClassBag
    {
        if (!is_array($class)) {
            $class = [$class];
        }
        $this->classes = array_unique(array_merge($this->classes, $class));
        return $this;
    }

    /**
     * Is a class in the list
     * 
     * @param  string  $class
     * @return boolean
     */
    public function has(string $class): bool
    {
        return in_array($class, $this->classes);
    }

    /**
     * Removes a class from the list
     * 
     * @param  string $class
     * @return ClassBag
     */
    public function remove(string $class): ClassBag
    {
        if ($index = array_search($class, $this->classes) !== false) {
            unset($this->classes[$index]);
        }
        return $this;
    }

    /**
     * Get all classes
     * 
     * @return array
     */
    public function get(): array
    {
        return $this->classes;
    }

    /**
     * Get classes html
     * 
     * @return string
     */
    public function toHtml(): string
    {
        return implode(' ', $this->classes);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}