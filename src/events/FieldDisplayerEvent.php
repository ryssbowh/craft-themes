<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategoryList;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategoryRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultColour;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultDate;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultDropdown;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultEmail;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultLightswitch;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultMultiSelect;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultNumber;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultPlainText;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultRadioButtons;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultRedactor;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultTags;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultTime;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultTitle;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DefaultUrl;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntryLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntryRendered;
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
            new DefaultRedactor,
            new DefaultColour,
            new AssetLink,
            new DefaultDate,
            new DefaultDropdown,
            new DefaultEmail,
            new EntryLink,
            new DefaultLightswitch,
            new DefaultMultiSelect,
            new DefaultNumber,
            new DefaultRadioButtons,
            new DefaultTags,
            new DefaultTime,
            new DefaultUrl,
            new EntryRendered,
            new CategoryRendered
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