<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\RegisterDisplayerTargetsEvent;
use Ryssbowh\CraftThemes\events\RegisterFileDefaultDisplayerEvent;
use Ryssbowh\CraftThemes\events\RegisterFileDisplayerEvent;
use Ryssbowh\CraftThemes\exceptions\FileDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use craft\helpers\Assets;
use yii\base\Event;

class FileDisplayerService extends Service
{
    const EVENT_REGISTER_DISPLAYERS = 'register_displayer';
    const EVENT_KIND_TARGETS = 'kind_targets';
    const EVENT_DEFAULT_DISPLAYERS = 'default_displayer';

    /**
     * @var string[]
     */
    protected $_displayers;

    /**
     * @var array
     */
    protected $_kindTargets = [];

    /**
     * @var string[]
     */
    protected $_defaults;

    /**
     * Displayers getter
     * 
     * @return array
     */
    public function getAll(): array
    {
        if ($this->_displayers === null) {
            $this->register();
        }
        return $this->_displayers;
    }

    /**
     * Get default displayers, indexed by kind
     * 
     * @return string[]
     */
    public function getDefaults(): array
    {
        if ($this->_defaults === null) {
            $this->registerDefaults();
        }
        return $this->_defaults;
    }

    /**
     * Get the kind targets for an displayer handle
     * 
     * @param  string $displayerHandle
     * @return string[]
     */
    public function getKindTargets(string $displayerHandle): array
    {
        if (!isset($this->_kindTargets[$displayerHandle])) {
            $this->registerTargets($displayerHandle);
        }
        return $this->_kindTargets[$displayerHandle];
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
        if (!isset($this->getAll()[$handle])) {
            throw FileDisplayerException::displayerNotDefined($handle);
        }
        return $this->getAll()[$handle];
    }

    /**
     * Get a displayer by handle
     * 
     * @param  string $handle
     * @return FileDisplayerInterface
     */
    public function getByHandle(string $handle): FileDisplayerInterface
    {
        $class = $this->getClassByHandle($handle);
        return new $class;
    }

    /**
     * Get displayers for an asset kind
     * 
     * @param  string $kind
     * @return FileDisplayerInterface[]
     */
    public function getForKind(string $kind): array
    {
        $handles = [];
        foreach ($this->getAll() as $handle => $class) {
            if (in_array($kind, $this->getKindTargets($handle))) {
                $handles[] = $handle;
            }
        }
        return $this->getByHandles($handles);
    }

    /**
     * Get displayers by handles
     * 
     * @param  array $handles
     * @return FileDisplayerInterface[]
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
        $event = new RegisterFileDisplayerEvent;
        $this->triggerEvent(self::EVENT_REGISTER_DISPLAYERS, $event);
        $this->_displayers = $event->getDisplayers();
    }

    /**
     * Register default displayers
     */
    protected function registerDefaults()
    {
        //figure out the default as defined by displayer classes :
        $defaults = [];
        foreach ($this->getAll() as $handle => $class) {
            foreach ($this->getKindTargets($handle) as $kind) {
                if ($class::isDefault($kind)) {
                    $defaults[$kind] = $handle;
                }
            }
        }
        //Give plugins opportunity to override default :
        $event = new RegisterFileDefaultDisplayerEvent([
            'defaults' => $defaults
        ]);
        $this->trigger(self::EVENT_DEFAULT_DISPLAYERS, $event);
        $this->_defaults = [];
        foreach ($event->defaults as $kind => $handle) {
            if (!isset($this->getAll()[$handle])) {
                continue;
            }
            if ($this->isDisplayerValidForKind($handle, $kind)) {
                $this->_defaults[$kind] = $handle;
            } else {
                $this->_defaults[$kind] = $defaults[$kind] ?? null;
            }
        }
    }

    /**
     * Register asset kind targets
     */
    protected function registerTargets(string $displayerHandle)
    {
        $displayerClass = $this->getClassByHandle($displayerHandle);
        $event = new RegisterDisplayerTargetsEvent([
            'targets' => $displayerClass::getKindTargets()
        ]);
        //Give plugins opportunity to register field targets
        Event::trigger($displayerClass, self::EVENT_KIND_TARGETS, $event);
        $this->_kindTargets[$displayerHandle] = $this->_getKindTargets($event->targets);
    }

    /**
     * Resolve an array of kinds, changing '*' to all defined kinds
     * 
     * @param  array  $kinds
     * @return array
     */
    protected function _getKindTargets(array $kinds): array
    {
        $out = [];
        foreach ($kinds as $handle) {
            if ($handle === '*') {
                $out = array_merge($out, array_keys(Assets::getFileKinds()));
            } else {
                $out[] = $handle;
            }
        }
        return array_unique($out);
    }

    /**
     * Is a displayer valid for a kind
     * 
     * @param  string  $displayerHandle
     * @param  string  $kind
     * @return boolean
     */
    protected function isDisplayerValidForKind(string $displayerHandle, string $kind): bool
    {
        return in_array($kind, $this->getKindTargets($displayerHandle));
    }
}