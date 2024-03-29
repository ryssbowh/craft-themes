<?php
namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class RenderEvent extends Event
{
    /**
     * @var string[]
     */
    public $templates;

    /**
     * @var array
     */
    public $variables;

    /**
     * Render this element or not
     * @var bool
     */
    public $render = true;

    /**
     * Prepend a template to the list of templates
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
     * Append a template to the list of templates
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
     * Add a variable to the list of variables
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
}