<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\HtmlAudioOptions;
use craft\base\Model;

class HtmlAudio extends FileDisplayer
{
    public static $handle = 'html_audio';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Html Audio');
    }

    public static function getKindTargets()
    {
        return ['audio'];
    }

    public function getOptionsModel(): Model
    {
        return new HtmlAudioOptions;
    }
}