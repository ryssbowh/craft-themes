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

class TagRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'tag_rendered';

    /**
     * @inheritDoc
     */
    public $hasOptions = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
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
    public function getTagLayout(): LayoutInterface
    {
        $group = $this->getTagGroup();
        $layout = Themes::$plugin->layouts->get($this->theme, LayoutService::TAG_HANDLE, $group->uid);
        return $layout;
    }

    /**
     * Get view modes associated to this displayer field's category group
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        $group = $this->getTagGroup();
        $layout = Themes::$plugin->layouts->get($this->getTheme(), LayoutService::TAG_HANDLE, $group->uid);
        $viewModes = [];
        foreach ($layout->getViewModes() as $viewMode) {
            $viewModes[$viewMode->uid] = $viewMode->name;
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
    protected function getTagGroup(): TagGroup
    {
        $elems = explode(':', $this->field->craftField->source);
        return \Craft::$app->tags->getTagGroupByUid($elems[1]);
    }
}