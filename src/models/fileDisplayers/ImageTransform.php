<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\ImageTransformOptions;
use craft\base\Model;

/**
 * Renders an image as a transform
 */
class ImageTransform extends FileDisplayer
{
    /**
     * @var string
     */
    public static $handle = 'image_transform';

    /**
     * @var boolean
     */
    public $hasOptions = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Image transform');
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
    public function getOptionsModel(): Model
    {
        return new ImageTransformOptions;
    }

    /**
     * Get all image transforms as array
     * 
     * @return array
     */
    public function getImageTransforms(): array
    {
        $out = [];
        foreach (\Craft::$app->assetTransforms->getAllTransforms() as $transform) {
            $out[$transform->handle] = $transform->name;
        }
        return $out;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['imageTransforms']);
    }
}