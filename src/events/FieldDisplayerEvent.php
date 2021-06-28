<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetRenderFile;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AuthorDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategoryList;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategoryRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\ColourDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DateDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DropdownDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EmailDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntryLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntryRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\FileDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\LightswitchDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MatrixDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MultiSelectDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\NumberDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\PlainTextFull;
use Ryssbowh\CraftThemes\models\fieldDisplayers\PlainTextTruncated;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RadioButtonsDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RedactorFull;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RedactorTruncated;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TableDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagsDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TimeDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TitleDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UrlDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UserInfoDefault;
use yii\base\Event;

class FieldDisplayerEvent extends Event
{
    /**
     * List of registered displayers
     * @var array
     */
    protected $displayers = [];

    /**
     * List of default displayers
     * @var array
     */
    protected $defaults = [];

    /**
     * Displayer mapping ['fieldClass' => [displayerHandle]]
     * @var array
     */
    protected $mapping = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->registerMany([
            AssetLink::class,
            AssetRendered::class,
            AssetRenderFile::class,
            AuthorDefault::class,
            CategoryRendered::class,
            CategoryList::class,
            ColourDefault::class,
            DateDefault::class,
            DropdownDefault::class,
            EmailDefault::class,
            EntryLink::class,
            EntryRendered::class,
            FileDefault::class,
            LightswitchDefault::class,
            MatrixDefault::class,
            MultiSelectDefault::class,
            NumberDefault::class,
            PlainTextFull::class,
            PlainTextTruncated::class,
            RadioButtonsDefault::class,
            RedactorFull::class,
            RedactorTruncated::class,
            TableDefault::class,
            TagsDefault::class,
            TimeDefault::class,
            TitleDefault::class,
            UrlDefault::class,
            UserInfoDefault::class
        ]);
    }

    /**
     * Default getter
     * 
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Displayers getter
     * 
     * @return array
     */
    public function getDisplayers(): array
    {
        return $this->displayers;
    }

    /**
     * Mapping getter
     * 
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    /**
     * Register a displayer class
     * 
     * @param string $class
     */
    public function register(string $class)
    {
        $this->displayers[$class::$handle] = $class;
        if (!isset($this->mapping[$class::getFieldTarget()])) {
            $this->mapping[$class::getFieldTarget()] = [];
        }
        if (!in_array($class::$handle, $this->mapping[$class::getFieldTarget()])) {
            $this->mapping[$class::getFieldTarget()][] = $class::$handle;
        }
        if ($class::$isDefault) {
            $this->defaults[$class::getFieldTarget()] = $class::$handle;
        }
    }

    /**
     * Register many displayer classes
     * 
     * @param array[string] $displayers
     */
    public function registerMany(array $displayers)
    {
        foreach ($displayers as $displayer) {
            $this->register($displayer);
        }
    }
}