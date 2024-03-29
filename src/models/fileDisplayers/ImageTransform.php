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
    public static $handle = 'image-transform';

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
    public static function getKindTargets(): array
    {
        return ['image'];
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        if ($this->options->transform == '_custom') {
            $custom = [];
            if ($params = json_decode($this->options['custom'], true)) {
                $custom[] = $params;
            }
            if ($sizes = json_decode($this->options->sizes, true)) {
                $custom = array_merge($custom, $sizes);
            }
            $eagerLoad = [[$eagerLoad[0], ['withTransforms' => $custom]]];
        } else if ($this->options->transform) {
            $eagerLoad = [[$eagerLoad[0], ['withTransforms' => [$this->options['transform']]]]];
        }
        return $eagerLoad;
    }

    /**
     * Get all image transforms as array
     * 
     * @return array
     */
    public function getImageTransforms(): array
    {
        $out = [];
        foreach (\Craft::$app->imageTransforms->getAllTransforms() as $transform) {
            $out[$transform->handle] = $transform->name;
        }
        return $out;
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return array_merge(parent::fields(), ['imageTransforms']);
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return ImageTransformOptions::class;
    }
}