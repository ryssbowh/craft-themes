<?php 

namespace Ryssbowh\CraftThemes\helpers;

class ClassBag
{
    protected $classes = [];

    public function __construct(array $classes = [])
    {
        $this->add($classes);
    }

    public function add($class): ClassBag
    {
        if (!is_array($class)) {
            $class = [$class];
        }
        $this->classes = array_unique(array_merge($this->classes, $class));
        return $this;
    }

    public function has(string $class): bool
    {
        return in_array($class, $this->classes);
    }

    public function remove(string $class): ClassBag
    {
        if ($index = array_search($class, $this->classes) !== false) {
            unset($this->classes[$index]);
        }
        return $this;
    }

    public function get(): array
    {
        return $this->classes;
    }

    public function toHtml(): string
    {
        return implode(' ', $this->classes);
    }

    public function __toString()
    {
        return $this->toHtml();
    }
}