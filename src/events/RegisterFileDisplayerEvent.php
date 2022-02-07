<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\FileDisplayerException;
use Ryssbowh\CraftThemes\models\fileDisplayers\Code;
use Ryssbowh\CraftThemes\models\fileDisplayers\HtmlAudio;
use Ryssbowh\CraftThemes\models\fileDisplayers\HtmlVideo;
use Ryssbowh\CraftThemes\models\fileDisplayers\Iframe;
use Ryssbowh\CraftThemes\models\fileDisplayers\ImageFull;
use Ryssbowh\CraftThemes\models\fileDisplayers\ImageTransform;
use Ryssbowh\CraftThemes\models\fileDisplayers\Link;
use Ryssbowh\CraftThemes\models\fileDisplayers\Raw;
use craft\helpers\Assets;
use yii\base\Event;

class RegisterFileDisplayerEvent extends Event
{
    /**
     * List of displayers
     * @var string[]
     */
    protected $_displayers = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->registerMany([
            Link::class,
            ImageTransform::class,
            ImageFull::class,
            HtmlAudio::class,
            Iframe::class,
            Raw::class,
            HtmlVideo::class,
            Code::class
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
     * @throws FileDisplayerException
     */
    public function register(string $class, bool $replaceIfExisting = false)
    {
        if (!$replaceIfExisting and isset($this->_displayers[$class::$handle])) {
            throw FileDisplayerException::alreadyDefined($class);
        }
        if (!preg_match('/^[a-zA-Z\-]+$/', $class::$handle)) {
            throw FileDisplayerException::handleInvalid($class);   
        }
        $this->_displayers[$class::$handle] = $class;
    }

    /**
     * Register many displayer classes
     * 
     * @param  string[] $displayers
     * @param  bool     $replaceIfExisting
     * @throws FileDisplayerException
     */
    public function registerMany(array $displayers, bool $replaceIfExisting = false)
    {
        foreach ($displayers as $displayer) {
            $this->register($displayer, $replaceIfExisting);
        }
    }
}