<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\HtmlVideoOptions;
use craft\base\Model;

class HtmlVideo extends FileDisplayer
{
    public static $handle = 'html_video';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Html Video');
    }

    public static function getKindTargets()
    {
        return ['video'];
    }

    public function getOptionsModel(): Model
    {
        return new HtmlVideoOptions;
    }
}