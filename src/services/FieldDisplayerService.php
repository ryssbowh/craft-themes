<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\RegisterFieldDisplayerEvent;
use Ryssbowh\CraftThemes\events\RegisterDisplayerTargetsEvent;
use Ryssbowh\CraftThemes\events\RegisterFieldDefaultDisplayerEvent;
use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\CraftFieldInterface;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use yii\base\Event;

class FieldDisplayerService extends Service
{
    const EVENT_REGISTER_DISPLAYERS = 'register_displayers';
    const EVENT_FIELD_TARGETS = 'field_targets';
    const EVENT_DEFAULT_DISPLAYER = 'default_dsplayer';

    /**
     * All displayers classes, indexed by handle
     * @var array
     */
    protected $_displayers;

    /**
     * Defaults displayers, indexed by field class
     * @var array
     */
    protected $_defaults = [];

    /**
     * Field targets, indexed by displayer handle
     * @var array
     */
    protected $_fieldTargets = [];

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
     * @param  string $fieldClass
     * @throws FieldDisplayerException
     */
    public function ensureDisplayerIsValidForField(string $displayerHandle, string $fieldClass)
    {
        $this->ensureDisplayerIsDefined($displayerHandle);
        $targets = $this->getFieldTargets($displayerHandle);
        if (!in_array($fieldClass, $targets)) {
            throw FieldDisplayerException::notValid($displayerHandle, $fieldClass);
        }
    }

    /**
     * Get the field targets for a displayer handle
     * 
     * @param  string $displayerHandle
     * @return array
     */
    public function getFieldTargets(string $displayerHandle): array
    {
        if (!isset($this->_fieldTargets[$displayerHandle])) {
            $this->registerTargets($displayerHandle);
        }
        return $this->_fieldTargets[$displayerHandle];
    }

    /**
     * Get available displayers for a field
     * 
     * @param  FieldInterface $fieldClass
     * @return array
     */
    public function getAvailable(FieldInterface $field): array
    {
        $available = [];
        foreach ($this->all() as $handle => $class) {
            if (in_array($field->getTargetClass(), $this->getFieldTargets($handle))) {
                $available[] = $handle;
            }
        }
        return $this->getByHandles($available);
    }

    /**
     * Get the default displayer handle for a field class
     * 
     * @param  string $classFor
     * @return ?string
     */
    public function getDefaultHandle(string $classFor): ?string
    {
        if (!isset($this->_defaults[$classFor])) {
            $this->registerDefault($classFor);
        }
        return $this->_defaults[$classFor] ?: null;
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
        $event = new RegisterFieldDisplayerEvent;
        $this->triggerEvent(self::EVENT_REGISTER_DISPLAYERS, $event);
        $this->_displayers = $event->getDisplayers();
    }

    /**
     * Register field targets for a displayer handle
     * 
     * @param string $displayerHandle
     */
    protected function registerTargets(string $displayerHandle)
    {
        $displayerClass = $this->getClassByHandle($displayerHandle);
        $event = new RegisterDisplayerTargetsEvent([
            'targets' => $displayerClass::getFieldTargets()
        ]);
        //Give plugins opportunity to register field targets
        Event::trigger($displayerClass, self::EVENT_FIELD_TARGETS, $event);
        $this->_fieldTargets[$displayerHandle] = array_unique($event->targets);
    }

    /**
     * Register default displayer handle for a field class
     * 
     * @param string $fieldClass
     */
    protected function registerDefault(string $fieldClass)
    {
        //figure out the default as defined by displayer classes :
        $default = '';
        foreach ($this->all() as $handle => $class) {
            foreach ($this->getKindTargets($handle) as $target) {
                if ($target == $fieldClass and $class::isDefault($fieldClass)) {
                    $default = $handle;
                    break;
                }
            }
        }
        //Give plugins opportunity to override default :
        $event = new RegisterFieldDefaultDisplayerEvent([
            'default' => $default
        ]);
        Event::trigger($fieldClass, self::EVENT_DEFAULT_DISPLAYER, $event);
        try {
            $this->ensureDisplayerIsValidForField($event->default, $fieldClass);
            $this->_defaults[$fieldClass] = $event->default;
        } catch (FieldDisplayerException $e) {
            $this->_defaults[$fieldClass] = $default;
        }
    }
}