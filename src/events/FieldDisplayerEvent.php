<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategoryList;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategoryRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\ColourDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DateDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DropdownDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EmailDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntryLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntryRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\LightswitchDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MatrixDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MultiSelectDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\NumberDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\PlainTextFull;
use Ryssbowh\CraftThemes\models\fieldDisplayers\PlainTextTrimmed;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RadioButtonsDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RedactorFull;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RedactorTrimmed;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagsDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TimeDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TitleDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UrlDefault;
use yii\base\Event;

class FieldDisplayerEvent extends Event
{
    protected $displayers = [];

    protected $defaults = [];

    protected $mapping = [];

    public function init()
    {
        $this->registerMany([
            new AssetLink,
            new AssetRendered,
            new CategoryRendered,
            new CategoryList,
            new ColourDefault,
            new DateDefault,
            new DropdownDefault,
            new EmailDefault,
            new EntryLink,
            new EntryRendered,
            new LightswitchDefault,
            new MatrixDefault,
            new MultiSelectDefault,
            new NumberDefault,
            new PlainTextFull,
            new PlainTextTrimmed,
            new RadioButtonsDefault,
            new RedactorFull,
            new RedactorTrimmed,
            new TagsDefault,
            new TimeDefault,
            new TitleDefault,
            new UrlDefault,
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