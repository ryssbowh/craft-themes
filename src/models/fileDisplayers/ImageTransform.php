<?php 

namespace Ryssbowh\CraftThemes\models\fileDisplayers;

use Ryssbowh\CraftThemes\models\FileDisplayer;
use Ryssbowh\CraftThemes\models\fileDisplayerOptions\ImageTransformOptions;
use craft\base\Model;

class ImageTransform extends FileDisplayer
{
    public static $handle = 'image_transform';

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Image transform');
    }

    public static function getKindTargets()
    {
        return ['image'];
    }

    public function getOptionsModel(): Model
    {
        return new ImageTransformOptions;
    }

    public function getImageTransforms(): array
    {
        $out = [];
        foreach (\Craft::$app->assetTransforms->getAllTransforms() as $transform) {
            $out[$transform->handle] = $transform->name;
        }
        return $out;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['imageTransforms']);
    }
}