<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class RenderEvent extends Event
{
    /**
     * @var array
     */
    public $templates;

    /**
     * @var array
     */
    public $variables;

    /**
     * Prepend a template to the list
     * 
     * @param  string $template
     * @return RenderEvent
     */
    public function prependTemplate(string $template): RenderEvent
    {
        array_unshift($this->templates, $template);
        return $this;
    }

    /**
     * Append a template to the list
     * 
     * @param  string $template
     * @return RenderEvent
     */
    public function appendTemplate(string $template): RenderEvent
    {
        $this->templates[] = $template;
        return $this;
    }

    /**
     * Add a variable to the list
     * 
     * @param  string $name
     * @param  mixed  $value
     * @return RenderEvent
     */
    public function addVariable(string $name, $value): RenderEvent
    {
        $this->variables[$name] = $value;
        return $this;
    }

    /**
     * Add an attribute(s) to the list
     * 
     * @param  string|array $name
     * @param  mixed|null   $value
     * @return RenderEvent
     */
    public function addAttribute($name, $value = null): RenderEvent
    {
        $this->variables['attributes']->add($name, $value);
        return $this;
    }

    /**
     * Add a class(es) to the list
     * 
     * @param  string|array $name
     * @return RenderEvent
     */
    public function addClass($name): RenderEvent
    {
        $this->variables['classes']->add($name);
        return $this;
    }
}