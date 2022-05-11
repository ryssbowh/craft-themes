<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
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
use Ryssbowh\CraftThemes\models\fieldDisplayers\ElementLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\ElementLinks;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EmailEmail;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntryRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\EntrySlick;
use Ryssbowh\CraftThemes\models\fieldDisplayers\FileFile;
use Ryssbowh\CraftThemes\models\fieldDisplayers\LightswitchLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\LinkFieldDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MatrixDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MatrixSlick;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MoneyDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\MultiSelectLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\NumberDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\PlainTextPlain;
use Ryssbowh\CraftThemes\models\fieldDisplayers\PlainTextTruncated;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RadioButtonsLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RedactorFull;
use Ryssbowh\CraftThemes\models\fieldDisplayers\RedactorTruncated;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TableDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagLabel;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagSlick;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagTitle;
use Ryssbowh\CraftThemes\models\fieldDisplayers\Time;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TimeAgo;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TitleTitle;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UrlLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UserDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UserRendered;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UserSlick;
use yii\base\Event;

class RegisterFieldDisplayerEvent extends Event
{
    /**
     * List of registered displayers
     * @var string[]
     */
    protected $_displayers = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->registerMany([
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
            ElementLink::class,
            ElementLinks::class,
            EntryRendered::class,
            EntrySlick::class,
            FileFile::class,
            LightswitchLabel::class,
            LinkFieldDefault::class,
            MatrixDefault::class,
            MatrixSlick::class,
            MoneyDefault::class,
            MultiSelectLabel::class,
            NumberDefault::class,
            PlainTextPlain::class,
            PlainTextTruncated::class,
            RadioButtonsLabel::class,
            RedactorFull::class,
            RedactorTruncated::class,
            TableDefault::class,
            TagLabel::class,
            TagTitle::class,
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
     * Displayers getter
     * 
     * @return string[]
     */
    public function getDisplayers(): array
    {
        return $this->_displayers;
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
        if (!$replaceIfExisting and isset($this->_displayers[$class::$handle])) {
            throw FieldDisplayerException::alreadyDefined($class, $this->_displayers[$class::$handle]);
        }
        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $class::$handle)) {
            throw FieldDisplayerException::handleInvalid($class);   
        }
        $this->_displayers[$class::$handle] = $class;
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