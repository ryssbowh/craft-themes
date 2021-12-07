<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\events\DefinableOptionsDefinitions;
use Ryssbowh\CraftThemes\exceptions\DefinableOptionsException;

/**
 * Base class for a set of options that have definitions that can be modified through an event
 */
abstract class EventDefinableOptions extends DefinableOptions
{
    const EVENT_OPTIONS_DEFINITIONS = 'options-definitions';

    /**
     * Register options definitions and default values through an event
     */
    protected function register()
    {
        $event = new DefinableOptionsDefinitions([
            'definitions' => $this->defineOptions(),
            'defaultValues' => $this->defineDefaultValues()
        ]);
        $this->trigger(self::EVENT_OPTIONS_DEFINITIONS, $event);
        $reserved = array_intersect(
            array_keys($event->definitions), 
            $this->reservedWords()
        );
        if (sizeof($reserved) > 0) {
            throw DefinableOptionsException::reserved(get_class($this), $reserved);
        }
        $this->_definitions = $event->definitions;
        $this->_defaultValues = $event->defaultValues;
    }
}