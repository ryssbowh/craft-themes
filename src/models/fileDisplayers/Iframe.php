<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\IframeOptions;
use craft\base\Model;

class Iframe extends FileDisplayer
{
    public static $handle = 'iframe';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Iframe');
    }

    public static function getKindTargets()
    {
        return ['html'];
    }

    public function getOptionsModel(): Model
    {
        return new IframeOptions;
    }
}