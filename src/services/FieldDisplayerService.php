<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\FieldDisplayerEvent;
use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\fields\Field;

class FieldDisplayerService extends Service
{
    const REGISTER_DISPLAYERS = 'register_displayer';

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

    public function getByHandle(string $handle, Field $field): FieldDisplayerInterface
    {
        if (!isset($this->all()[$handle])) {
            throw FieldDisplayerException::displayerNotDefined($handle);
        }
        $class = $this->all()[$handle];
        $class = new $class(['field' => $field]);
        return $class;
    }

    public function getByHandles(array $handles, Field $field): array
    {
        $_this = $this;
        return array_map(function ($handle) use ($_this, $field) {
            return $_this->getByHandle($handle, $field);
        }, $handles);
    }

    public function getForField(string $classFor, Field $field): array
    {
        return $this->getByHandles($this->getMapping()[$classFor] ?? [], $field);
    }

    public function getDefaultHandle(string $classFor): ?string
    {
        return $this->getDefaults()[$classFor] ?? null;
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