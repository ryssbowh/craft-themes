<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\FieldDisplayerEvent;
use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;

class FieldDisplayerService extends Service
{
    const REGISTER_DISPLAYERS = 1;

    protected $_defaults;
    protected $_displayers;
    protected $_mapping;

    public function getDefaults(): array
    {
        if (is_null($this->_defaults)) {
            $this->register();
        }
        return $this->_defaults;
    }

    public function getMapping(): array
    {
        if (is_null($this->_mapping)) {
            $this->register();
        }
        return $this->_mapping;
    }

    public function all(): array
    {
        if (is_null($this->_displayers)) {
            $this->register();
        }
        return $this->_displayers;
    }

    public function getByHandle(string $handle): FieldDisplayerInterface
    {
        if (!isset($this->all()[$handle])) {
            throw FieldDisplayerException::displayerNotDefined($handle);
        }
        return $this->all()[$handle];
    }

    public function getByHandles(array $handles): array
    {
        $_this = $this;
        return array_map(function ($handle) use ($_this) {
            return $_this->getByHandle($handle);
        }, $handles);
    }

    public function getForField(string $fieldClass): array
    {
        return $this->getByHandles($this->getMapping()[$fieldClass] ?? []);
    }

    public function getDefault(string $fieldClass): ?FieldDisplayerInterface
    {
        if ($default = $this->getDefaults()[$fieldClass] ?? false) {
            return $this->getByHandle($default);
        }
        if ($default = $this->getForField($fieldClass)[0] ?? false) {
            return $default;
        }
        return null;
    }

    protected function register()
    {
        if ($this->_defaults !== null) {
            return;
        }
        $event = new FieldDisplayerEvent;
        $this->triggerEvent(self::REGISTER_DISPLAYERS, $event);
        $this->_defaults = $event->getDefaults();
        $this->_displayers = $event->getDisplayers();
        $this->_mapping = $event->getMapping();
    }
}