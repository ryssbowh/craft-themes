<?php 

namespace Ryssbowh\CraftThemes\helpers;

class AttributeBag
{
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->add($attributes);
    }

    public function add($index, $value = null): AttributeBag
    {
        if (!is_array($index)) {
            $index = [$index => $value];
        }
        $this->classes = array_merge($this->attributes, $index);
        return $this;
    }

    public function has(string $index): bool
    {
        return isset($this->attributes[$index]);
    }

    public function remove(string $index): AttributeBag
    {
        if ($this->has($index)) {
            unset($this->attributes[$index]);
        }
        return $this;
    }

    public function get(): array
    {
        return $this->attributes;
    }

    public function toHtml(): string
    {
        $html = [];
        foreach ($this->attributes as $index => $value) {
            $html[] = $index . '="' . $value . '"';
        }
        return implode(' ', $html);
    }

    public function __toString()
    {
        return $this->toHtml();
    }
}