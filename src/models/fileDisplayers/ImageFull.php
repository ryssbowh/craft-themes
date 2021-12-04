<?php
namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\ImageFullOptions;
use craft\base\Model;

/**
 * Renders an image full size
 */
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
    public static function isDefault(string $kind): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getKindTargets()
    {
        return ['image'];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return ImageFullOptions::class;
    }
}