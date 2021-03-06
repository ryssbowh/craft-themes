<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategoryList;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultPlainText;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultTitle;
use yii\base\Event;

class FieldDisplayerEvent extends Event
{
    protected $displayers = [];

    protected $defaults = [];

    protected $mapping = [];

    public function init()
    {
        $this->registerMany([
            new DefaultPlainText,
            new DefaultTitle,
            new CategoryList,
        ]);
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    public function getDisplayers(): array
    {
        return $this->displayers;
    }

    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function register(FieldDisplayerInterface $class)
    {
        if (!$class->handle) {
            throw FieldDisplayerException::noHandle($class);
        }
        $this->displayers[$class->handle] = $class;
        if (!isset($this->mapping[$class->getFieldTarget()])) {
            $this->mapping[$class->getFieldTarget()] = [];
        }
        if (!in_array($class->handle, $this->mapping[$class->getFieldTarget()])) {
            $this->mapping[$class->getFieldTarget()][] = $class->handle;
        }
        if ($class->isDefault) {
            $this->defaults[$class->getFieldTarget()] = $class->handle;
        }
    }

    public function registerMany(array $displayers)
    {
        foreach ($displayers as $displayer) {
            $this->register($displayer);
        }
    }
}