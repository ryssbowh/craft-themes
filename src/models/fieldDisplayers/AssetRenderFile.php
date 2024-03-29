<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FileFieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetRenderFileOptions;
use Ryssbowh\CraftThemes\models\fields\UserPhoto;
use craft\fields\Assets;
use craft\helpers\Assets as AssetsHelper;

/**
 * Renders the file of an asset field
 */
class AssetRenderFile extends FieldDisplayer implements FileFieldDisplayerInterface
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset-renderfile';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Render file');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Assets::class, UserPhoto::class];
    }

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return $fieldClass == UserPhoto::class;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayerForKind(string $kind): ?FileDisplayerInterface
    {
        return $this->options->getDisplayerForKind($kind);
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        foreach ($this->options->displayers as $kind => $options) {
            if ($displayer = $this->getDisplayerForKind($kind)) {
                $eagerLoad = $displayer->eagerLoad($eagerLoad, $prefix, $level);
            }
        }
        return $eagerLoad;
    }

    /**
     * Get available file kinds
     * 
     * @return array
     */
    public function getAllowedFileKinds(): array
    {
        $kinds = AssetsHelper::getFileKinds();
        if ($this->field instanceof UserPhoto) {
            return ['image' => $kinds['image']];
        }
        if ($this->field->craftField->restrictFiles) {
            $allowed = $this->field->craftField->allowedKinds;
            $kinds = array_filter($kinds, function ($key) use ($allowed) {
                return in_array($key, $allowed);
            }, ARRAY_FILTER_USE_KEY);
        }
        return $kinds;
    }

    /**
     * Get field limit
     * 
     * @return int
     */
    public function getLimit(): ?int
    {
        if ($this->field instanceof UserPhoto) {
            return 1;
        }
        return $this->field->craftField->minRelations;
    }

    /**
     * @inheritDoc
     */
    public function getCanBeCached(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return AssetRenderFileOptions::class;
    }
}