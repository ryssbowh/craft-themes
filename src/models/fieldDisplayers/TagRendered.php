<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\ViewModesHelper;
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
    public static $handle = 'tag-rendered';

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
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        foreach ($this->getViewModes() as $uid => $label) {
            $viewMode = Themes::$plugin->viewModes->getByUid($uid);
            //Avoid infinite loops for self referencing view modes :
            if ($viewMode->id != $this->field->viewMode->id) {
                $eagerLoad = array_merge($eagerLoad, $viewMode->eagerLoad($prefix, $level));
            }
        }
        return $eagerLoad;
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

    /**
     * Get view modes available, based on the field tag
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        return ViewModesHelper::getTagGroupViewModes($this->field->craftField, $this->getTheme());
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return TagRenderedOptions::class;
    }
}