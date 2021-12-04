<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\FieldDisplayerEvent;
use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\fields\CraftField;

class FieldDisplayerService extends Service
{
    const REGISTER_DISPLAYERS = 'register_displayer';

    /**
     * Defaults displayers, indexed by field class
     * @var array
     */
    protected $_defaults;

    /**
     * All displayers, indexed by handle
     * @var array
     */
    protected $_displayers;

    /**
     * displayer/fields mapping
     * @var array
     */
    protected $_mapping;

    /**
     * Defaults getter
     * 
     * @return array
     */
    public function getDefaults(): array
    {
        if (is_null($this->_defaults)) {
            $this->register();
        }
        return $this->_defaults;
    }

    /**
     * Mapping getter
     * 
     * @return array
     */
    public function getMapping(): array
    {
        if (is_null($this->_mapping)) {
            $this->register();
        }
        return $this->_mapping;
    }

    /**
     * Displayers getter
     * 
     * @return array
     */
    public function all(): array
    {
        if (is_null($this->_displayers)) {
            $this->register();
        }
        return $this->_displayers;
    }

    /**
     * Does a displayer handle exists
     * 
     * @param  string  $handle
     * @return boolean
     */
    public function hasDisplayer(string $handle)
    {
        return isset($this->all()[$handle]);
    }

    /**
     * Get a displayer class by handle
     * 
     * @param  string $handle
     * @throws FieldDisplayerException
     * @return string
     */
    public function getClassByHandle(string $handle): string
    {
        $this->ensureDisplayerIsDefined($handle);
        return $this->all()[$handle];
    }

    /**
     * Get a displayer by handle
     * 
     * @param  string $handle
     * @throws FieldDisplayerException
     * @return FieldDisplayerInterface
     */
    public function getByHandle(string $handle): FieldDisplayerInterface
    {
        $class = $this->getClassByHandle($handle);
        return new $class;
    }

    /**
     * Ensure a displayer handle is valid for a field class
     * 
     * @param  string $displayerHandle
     * @param  Field  $field
     * @throws FieldDisplayerException
     */
    public function ensureDisplayerIsValidForField(string $displayerHandle, Field $field)
    {
        $this->ensureDisplayerIsDefined($displayerHandle);
        $fieldClass = get_class($field);
        if ($field instanceof CraftField) {
            //The field target for a craft field is the craft field itself
            $fieldClass = get_class($field->craftField);
        }
        if (!in_array($displayerHandle, $this->getMapping()[$fieldClass] ?? [])) {
            throw FieldDisplayerException::notValid($displayerHandle, $fieldClass);
        }
    }

    /**
     * Get displayers for a field
     * 
     * @param  string $classFor
     * @return array
     */
    public function getForField(string $classFor): array
    {
        return $this->getByHandles($this->getMapping()[$classFor] ?? []);
    }

    /**
     * Get the default displayer handle for a field class
     * 
     * @param  string $classFor
     * @return ?string
     */
    public function getDefaultHandle(string $classFor): ?string
    {
        return $this->getDefaults()[$classFor] ?? null;
    }

    /**
     * Get all displayers indexed by the field target (either the craft field class or the field class)
     *
     * @return array
     */
    public function getAllByFieldTarget(): array
    {
        $displayers = [];
        foreach ($this->all() as $displayer) {
            foreach ($displayer::getFieldTargets() as $fieldTarget) {
                $displayers[$fieldTarget][] = new $displayer;
            }
        }
        return $displayers;
    }

    /**
     * Get many displayers, by handle
     * 
     * @param  array $handles
     * @return array
     */
    protected function getByHandles(array $handles): array
    {
        $_this = $this;
        $displayers = [];
        foreach ($handles as $handle) {
            $displayer = $this->getByHandle($handle);
            if ($displayer) {
                $displayers[] = $displayer;
            }
        }
        return $displayers;
    }

    /**
     * Ensures a displayer handle is defined
     * 
     * @param  string $handle
     * @throws FieldDisplayerException
     */
    protected function ensureDisplayerIsDefined(string $handle)
    {
        if (!$this->hasDisplayer($handle)) {
            throw FieldDisplayerException::notDefined($handle);
        }
    }

    /**
     * Registers field displayers
     */
    protected function register()
    {
        $event = new FieldDisplayerEvent;
        $this->triggerEvent(self::REGISTER_DISPLAYERS, $event);
        $this->_defaults = $event->getDefaults();
        $this->_displayers = $event->getDisplayers();
        $this->_mapping = $event->getMapping();
    }
}