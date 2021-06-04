<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\models\fileDisplayers\HtmlAudio;
use Ryssbowh\CraftThemes\models\fileDisplayers\HtmlVideo;
use Ryssbowh\CraftThemes\models\fileDisplayers\Iframe;
use Ryssbowh\CraftThemes\models\fileDisplayers\ImageFull;
use Ryssbowh\CraftThemes\models\fileDisplayers\ImageTransform;
use Ryssbowh\CraftThemes\models\fileDisplayers\Link;
use Ryssbowh\CraftThemes\models\fileDisplayers\Raw;
use craft\helpers\Assets;
use yii\base\Event;

class FileDisplayerEvent extends Event
{
    protected $displayers = [];

    protected $mapping = [];

    public function init()
    {
        $this->registerMany([
            Link::class,
            ImageTransform::class,
            ImageFull::class,
            HtmlAudio::class,
            Iframe::class,
            Raw::class,
            HtmlVideo::class
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

    public function register(string $class)
    {
        $this->displayers[$class::$handle] = $class;
        $kinds = $class::getKindTargets();
        if ($kinds == '*') {
            $kinds = array_keys(Assets::getFileKinds());
        }
        foreach ($kinds as $kind) {
            $this->mapping[$kind][] = $class::$handle;
        }
    }

    public function registerMany(array $displayers)
    {
        foreach ($displayers as $displayer) {
            $this->register($displayer);
        }
    }
}