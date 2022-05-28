<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AssetLinksOptions;
use craft\fields\Assets;

/**
 * Renders some assets as links
 *
 * @since 4.1.0
 */
class AssetLinks extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'asset-links';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Links');
    }

    /**
     * Get field limit
     * 
     * @return int
     */
    public function getLimit(): ?int
    {
        return $this->field->craftField->minRelations;
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Assets::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return AssetLinksOptions::class;
    }
}