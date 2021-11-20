<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\FileDisplayerEvent;
use Ryssbowh\CraftThemes\exceptions\FileDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;

class FileDisplayerService extends Service
{
    const REGISTER_DISPLAYERS = 'register_displayer';

    /**
     * @var array
     */
    protected $_displayers;

    /**
     * Displayer mapping, indexed by asset kinds
     * @var array
     */
    protected $_mapping;

    /**
     * Defaults displayer mapping, indexed by asset kinds
     * @var array
     */
    protected $_defaults;

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
     * @return FileDisplayerInterface
     */
    public function getByHandle(string $handle): FileDisplayerInterface
    {
        if (!isset($this->all()[$handle])) {
            throw FileDisplayerException::displayerNotDefined($handle);
        }
        $class = $this->all()[$handle];
        return new $class;
    }

    /**
     * Get displayers for an asset kind
     * 
     * @param  string $kind
     * @return array
     */
    public function getForKind(string $kind): array
    {
        return $this->getByHandles($this->getMapping()[$kind] ?? []);
    }

    /**
     * Get displayers by handles
     * 
     * @param  array  $handles
     * @return array
     */
    protected function getByHandles(array $handles): array
    {
        $_this = $this;
        return array_map(function ($handle) use ($_this) {
            return $_this->getByHandle($handle);
        }, $handles);
    }

    /**
     * Registers displayers
     */
    protected function register()
    {
        $event = new FileDisplayerEvent;
        $this->triggerEvent(self::REGISTER_DISPLAYERS, $event);
        $this->_displayers = $event->getDisplayers();
        $this->_mapping = $event->getMapping();
        $this->_defaults = $event->getDefaults();
    }
}