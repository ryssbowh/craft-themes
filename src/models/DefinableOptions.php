<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\exceptions\DefinableOptionsException;
use craft\base\Model;

/**
 * Base class for a set of options that have definitions
 */
abstract class DefinableOptions extends Model
{
    /**
     * @var array
     */
    protected $_defaultValues;

    /**
     * @var array
     */
    protected $_values = [];

    /**
     * @var array
     */
    protected $_definitions;

    /**
     * Get the options definitions.
     * Throws an event to allow other plugins to modify the options
     * 
     * @return array
     */
    public function getDefinitions(): array
    {
        if ($this->_definitions === null) {
            $this->register();
        }
        return $this->_definitions;
    }

    /**
     * Get all default values
     * 
     * @return array
     */
    public function getDefaultValues(): array
    {
        if ($this->_defaultValues === null) {
            $this->register();
        }
        return $this->_defaultValues;
    }

    /**
     * Define the options
     * 
     * @return array
     */
    public function defineOptions(): array
    {
        return [];
    }

    /**
     * Define the default values
     * 
     * @return array
     */
    public function defineDefaultValues(): array
    {
        return [];
    }

    /**
     * Get all values
     * 
     * @return array
     */
    public function getValues(): array
    {
        return array_merge($this->defaultValues, $this->_values);
    }

    /**
     * Set all values
     * 
     * @param array $values
     */
    public function setValues($values)
    {
        foreach ((array)$values as $name => $value) {
            $this->setValue($name, $value);
        }
    }

    /**
     * Replace all values
     * 
     * @param array $values
     */
    public function replaceValues($values)
    {
        $this->_values = (array)$values;
    }

    /**
     * Get the value for an option
     * 
     * @param  string $name
     * @return mixed
     */
    public function getValue(string $name)
    {
        return $this->_values[$name] ?? $this->defaultValues[$name] ?? null;
    }

    /**
     * Set value for an option
     * 
     * @param string $name
     * @param mixed  $value
     */
    public function setValue(string $name, $value)
    {
        $this->_values[$name] = $value;
    }

    /**
     * Is an options defined
     * 
     * @param  string  $name
     * @return boolean
     */
    public function hasOption(string $name): bool
    {
        return isset($this->definitions[$name]);
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), array_keys($this->definitions));
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $config = [];
        foreach ($this->definitions as $name => $definition) {
            $save = $definition['saveInConfig'] ?? true;
            if ($save) {
                $config[$name] = $this->$name;
            }
        }
        return $config;
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return ['definitions', 'defaultValues'];
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        if (!in_array($name, $this->reservedWords()) && $this->hasOption($name)) {
            return $this->getValue($name);
        }
        return parent::__get($name);
    }

    /**
     * @inheritDoc
     */
    public function __isset($name)
    {
        if (!in_array($name, $this->reservedWords()) && $this->hasOption($name)) {
            return true;
        }
        return parent::__isset($name);
    }

    /**
     * @inheritDoc
     */
    public function __set($name, $value)
    {
        if (!in_array($name, $this->reservedWords()) && $this->hasOption($name)) {
            return $this->setValue($name, $value);
        }
        return parent::__set($name, $value);
    }

    /**
     * Register options definitions and default values
     */
    protected function register()
    {
        $definitions = $this->defineOptions();
        $reserved = array_intersect(
            array_keys($definitions), 
            $this->reservedWords()
        );
        if (sizeof($reserved) > 0) {
            throw DefinableOptionsException::reserved(get_class($this), $reserved);
        }
        $this->_definitions = $definitions;
        $this->_defaultValues = $this->defineDefaultValues();
    }

    /**
     * Reserved words that options can't take
     * 
     * @return array
     */
    protected function reservedWords(): array
    {
        return ['definitions', 'values', 'value', 'defaultValues'];
    }
}