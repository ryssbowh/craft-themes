<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\LinkOptions;
use craft\base\Model;

class Link extends FileDisplayer
{
    public static $handle = 'link';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Link to asset');
    }

    public static function getKindTargets()
    {
        return '*';
    }

    public function getOptionsModel(): Model
    {
        return new LinkOptions;
    }
}