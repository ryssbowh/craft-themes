<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\FileDisplayerEvent;
use Ryssbowh\CraftThemes\exceptions\FileDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;

class FileDisplayerService extends Service
{
    const REGISTER_DISPLAYERS = 'register_displayer';

    protected $_displayers;
    protected $_mapping;

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

    public function getByHandle(string $handle): FileDisplayerInterface
    {
        if (!isset($this->all()[$handle])) {
            throw FileDisplayerException::displayerNotDefined($handle);
        }
        $class = $this->all()[$handle];
        return new $class;
    }

    public function getByHandles(array $handles): array
    {
        $_this = $this;
        return array_map(function ($handle) use ($_this) {
            return $_this->getByHandle($handle);
        }, $handles);
    }

    public function getForKind(string $kind): array
    {
        return $this->getByHandles($this->getMapping()[$kind] ?? []);
    }

    protected function register()
    {
        if ($this->_displayers !== null) {
            return;
        }
        $event = new FileDisplayerEvent;
        $this->triggerEvent(self::REGISTER_DISPLAYERS, $event);
        $this->_displayers = $event->getDisplayers();
        $this->_mapping = $event->getMapping();
    }
}