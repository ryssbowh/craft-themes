<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use craft\base\Model;

class Raw extends FileDisplayer
{
    public static $handle = 'raw';

    public function getName(): string
    {
        return \Craft::t('themes', 'Raw');
    }

    public static function getKindTargets()
    {
        return ['javascript', 'html', 'php', 'text', 'xml'];
    }
}