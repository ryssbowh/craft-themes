<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;

class ImageFull extends FileDisplayer
{
    /**
     * @var string
     */
    public static $handle = 'image_full';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Full image');
    }

    /**
     * @inheritDoc
     */
    public static function getKindTargets()
    {
        return ['image'];
    }
}