<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TagRenderedOptions;
use craft\fields\Tags;

/**
 * Renders a tag field as rendered using a view mode
 */
class TagRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'tag_rendered';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered as view mode');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Tags::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return TagRenderedOptions::class;
    }

    /**
     * Get the layout associated to this displayer field's category group
     * 
     * @return LayoutInterface
     */
    public function getTagLayout(): ?LayoutInterface
    {
        $elems = explode(':', $this->field->craftField->source);
        $group = \Craft::$app->tags->getTagGroupByUid($elems[1]);
        if ($group) {
            return $group->getLayout($this->theme);
        }
        return null;
    }
}