<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TagRenderedOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Model;
use craft\fields\Tags;
use craft\models\TagGroup;

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
    public static function getFieldTarget(): string
    {
        return Tags::class;
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
        if ($group = $this->getTagGroup()) {
            return $group->getLayout($this->theme);
        }
        return null;
    }

    /**
     * Get view modes associated to this displayer field's category group
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        $viewModes = [];
        if ($group = $this->getTagGroup()) {
            $layout = $group->getLayout($this->theme);
            foreach ($layout->getViewModes() as $viewMode) {
                $viewModes[$viewMode->uid] = $viewMode->name;
            }
        }
        return $viewModes;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['viewModes']);
    }

    /**
     * get the category group defined on this displayer's field
     * 
     * @return CategoryGroup
     */
    protected function getTagGroup(): ?TagGroup
    {
        $elems = explode(':', $this->field->craftField->source);
        return \Craft::$app->tags->getTagGroupByUid($elems[1]);
    }
}