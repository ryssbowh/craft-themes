<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\ElementLinkOptions;
use Ryssbowh\CraftThemes\models\fields\ElementUrl;
use craft\fields\Categories;
use craft\fields\Entries;

/**
 * Renders one element's url as link
 */
class ElementLink extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'element_link';

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
        return \Craft::t('themes', 'Link');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [ElementUrl::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return ElementLinkOptions::class;
    }
}