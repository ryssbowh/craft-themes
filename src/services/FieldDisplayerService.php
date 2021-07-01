<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\FieldDisplayerEvent;
use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\fields\Field;

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
     * Get a displayer by handle
     * 
     * @param  string $handle
     * @param  Field  $field
     * @return ?FieldDisplayerInterface
     */
    public function getByHandle(string $handle, Field $field): ?FieldDisplayerInterface
    {
        if (!isset($this->all()[$handle])) {
            return null;
        }
        $class = $this->all()[$handle];
        $class = new $class(['field' => $field]);
        return $class;
    }

    /**
     * Get displayers for a field
     * 
     * @param  string $classFor
     * @param  Field  $field
     * @return array
     */
    public function getForField(string $classFor, Field $field): array
    {
        return $this->getByHandles($this->getMapping()[$classFor] ?? [], $field);
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
     * Get many displayers, by handle
     * 
     * @param  array  $handles
     * @param  Field  $field
     * @return array
     */
    protected function getByHandles(array $handles, Field $field): array
    {
        $_this = $this;
        $displayers = [];
        foreach ($handles as $handle) {
            $displayer = $this->getByHandle($handle, $field);
            if ($displayer) {
                $displayers[] = $displayer;
            }
        }
        return $displayers;
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