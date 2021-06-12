<?php 

namespace Ryssbowh\CraftThemes\events;

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

class FileDisplayerEvent extends Event
{
    /**
     * List of displayers
     * @var array
     */
    protected $displayers = [];

    /**
     * Displayer mapping ['assetKind' => ['displayerHandle']]
     * @var array
     */
    protected $mapping = [];

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
     */
    public function register(string $class)
    {
        $this->displayers[$class::$handle] = $class;
        $kinds = $class::getKindTargets();
        if ($kinds == '*') {
            $kinds = array_keys(Assets::getFileKinds());
        }
        foreach ($kinds as $kind) {
            if (!in_array($class::$handle, $this->mapping[$kind] ?? [])) {
                $this->mapping[$kind][] = $class::$handle;
            }
        }
    }

    /**
     * Register many displayer classes
     * 
     * @param  array[string] $displayers
     */
    public function registerMany(array $displayers)
    {
        foreach ($displayers as $displayer) {
            $this->register($displayer);
        }
    }
}