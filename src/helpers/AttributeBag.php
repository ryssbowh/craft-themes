<?php 

namespace Ryssbowh\CraftThemes\helpers;

class AttributeBag
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @inheritDoc
     */
    public function __construct(array $attributes = [])
    {
        $this->add($attributes);
    }

    /**
     * Add an attribute. First argument can be an array of attributes
     * 
     * @param  array|string $index
     * @param  mixed $value
     * @return AttributeBag
     */
    public function add($index, $value = null): AttributeBag
    {
        if (!is_array($index)) {
            $index = [$index => $value];
        }
        $this->classes = array_merge($this->attributes, $index);
        return $this;
    }

    /**
     * Does an attribute exist
     * 
     * @param  string  $index
     * @return boolean
     */
    public function has(string $index): bool
    {
        return isset($this->attributes[$index]);
    }

    /**
     * Removes an argument
     * 
     * @param  string $index
     * @return AttributeBag
     */
    public function remove(string $index): AttributeBag
    {
        if ($this->has($index)) {
            unset($this->attributes[$index]);
        }
        return $this;
    }

    /**
     * Get all attributes
     * 
     * @return array
     */
    public function get(): array
    {
        return $this->attributes;
    }

    /**
     * Get html string for all arguments
     * 
     * @return string
     */
    public function toHtml(): string
    {
        $html = [];
        foreach ($this->attributes as $index => $value) {
            $html[] = $index . '="' . $value . '"';
        }
        return implode(' ', $html);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}