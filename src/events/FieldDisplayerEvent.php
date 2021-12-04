<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetRenderFile;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetSlick;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategoryLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategoryRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CategorySlick;
use Ryssbowh\CraftThemes\models\fieldDisplayers\CheckboxesLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\ColourDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\Date;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DateTime;
use Ryssbowh\CraftThemes\models\fieldDisplayers\DropdownLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EmailEmail;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntryLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntryRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntrySlick;
use Ryssbowh\CraftThemes\models\fieldDisplayers\FileFile;
use Ryssbowh\CraftThemes\models\fieldDisplayers\LightswitchLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MatrixDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MatrixSlick;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MultiSelectLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\NumberDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\PlainTextFull;
use Ryssbowh\CraftThemes\models\fieldDisplayers\PlainTextTruncated;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RadioButtonsLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RedactorFull;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RedactorTruncated;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TableDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagSlick;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagTitleTitle;
use Ryssbowh\CraftThemes\models\fieldDisplayers\Time;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TimeAgo;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TitleTitle;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UrlLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UserDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UserRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UserSlick;
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
        parent::init();
        $this->registerMany([
            AssetLink::class,
            AssetSlick::class,
            AssetRendered::class,
            AssetRenderFile::class,
            CategoryRendered::class,
            CategoryLabel::class,
            CategorySlick::class,
            CheckboxesLabel::class,
            ColourDefault::class,
            Date::class,
            DateTime::class,
            DropdownLabel::class,
            EmailEmail::class,
            EntryLink::class,
            EntryRendered::class,
            EntrySlick::class,
            FileFile::class,
            LightswitchLabel::class,
            MatrixDefault::class,
            MatrixSlick::class,
            MultiSelectLabel::class,
            NumberDefault::class,
            PlainTextFull::class,
            PlainTextTruncated::class,
            RadioButtonsLabel::class,
            RedactorFull::class,
            RedactorTruncated::class,
            TableDefault::class,
            TagLabel::class,
            TagTitleTitle::class,
            TagRendered::class,
            TagSlick::class,
            Time::class,
            TimeAgo::class,
            TitleTitle::class,
            UrlLink::class,
            UserDefault::class,
            UserRendered::class,
            UserSlick::class,
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
     * @param  string $class
     * @param  bool   $replaceIfExisting
     * @throws FieldDisplayerException
     */
    public function register(string $class, bool $replaceIfExisting = false)
    {
        if (!$replaceIfExisting and isset($this->displayers[$class::$handle])) {
            throw FieldDisplayerException::alreadyDefined($class);
        }
        $this->displayers[$class::$handle] = $class;
        foreach ($class::getFieldTargets() as $fieldTarget) {
            if (!isset($this->mapping[$fieldTarget])) {
                $this->mapping[$fieldTarget] = [];
            }
            if (!in_array($class::$handle, $this->mapping[$fieldTarget])) {
                $this->mapping[$fieldTarget][] = $class::$handle;
            }
            if ($class::isDefault($fieldTarget)) {
                $this->defaults[$fieldTarget] = $class::$handle;
            }
        }
    }

    /**
     * Register many displayer classes
     * 
     * @param  array $displayers
     * @param  bool  $replaceIfExisting
     * @throws FieldDisplayerException
     */
    public function registerMany(array $displayers, bool $replaceIfExisting = false)
    {
        foreach ($displayers as $displayer) {
            $this->register($displayer, $replaceIfExisting);
        }
    }
}