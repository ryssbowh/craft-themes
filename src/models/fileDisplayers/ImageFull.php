<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;

class ImageFull extends FileDisplayer
{
    public static $handle = 'image_full';

    public function getName(): string
    {
        return \Craft::t('themes', 'Full image');
    }

    public static function getKindTargets()
    {
        return ['image'];
    }
}