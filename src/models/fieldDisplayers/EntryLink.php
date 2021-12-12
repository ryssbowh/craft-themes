<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EntryLinkOptions;
use Ryssbowh\CraftThemes\models\fields\ElementUrl;
use craft\fields\Entries;

/**
 * Renders an entry field as links
 */
class EntryLink extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'entry_link';

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
        return \Craft::t('themes', 'Link to entry');
    }

    /**
     * Get field limit
     * 
     * @return int
     */
    public function getLimit(): ?int
    {
        if ($this->field instanceof ElementUrl) {
            return 1;
        }
        return $this->field->craftField->limit;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        if ($this->field instanceof ElementUrl) {
            $value = [Themes::$plugin->view->renderingElement];
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Entries::class, ElementUrl::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return EntryLinkOptions::class;
    }
}