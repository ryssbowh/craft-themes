<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\ElementLinksOptions;
use Ryssbowh\CraftThemes\models\fields\ElementUrl;
use craft\fields\Assets;
use craft\fields\Categories;
use craft\fields\Entries;

/**
 * Renders some elements as links
 */
class ElementLinks extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'element-links';

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
        return [Entries::class, Categories::class, Assets::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return ElementLinksOptions::class;
    }
}