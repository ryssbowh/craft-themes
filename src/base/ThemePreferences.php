<?php
namespace Ryssbowh\CraftThemes\base;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\RegionInterface;
use Ryssbowh\CraftThemes\interfaces\ThemePreferencesInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\services\ViewService;
use craft\base\Component;
use craft\elements\Asset;

/**
 * Default class for all theme preferences
 */
class ThemePreferences extends Component implements ThemePreferencesInterface
{
    /**
     * @var ViewService
     */
    protected $view;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->view = Themes::$plugin->view;
    }

    /**
     * @inheritDoc
     */
    public function getLayoutClasses(LayoutInterface $layout, bool $root = false): array
    {
        return ['layout'];
    }

    /**
     * @inheritDoc
     */
    public function getLayoutAttributes(LayoutInterface $layout, bool $root = false): array
    {
        return [
            'data-viewmode' => $this->viewMode->handle,
            'data-handle' => $layout->getTemplatingKey(),
            'data-type' => $layout->type
        ];
    }

    /**
     * @inheritDoc
     */
    public function getBlockClasses(BlockInterface $block): array
    {
        return ['block'];
    }

    /**
     * @inheritDoc
     */
    public function getBlockAttributes(BlockInterface $block): array
    {
        return [
            'data-handle' => 'block-' . $block->getMachineName()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getRegionClasses(RegionInterface $region): array
    {
        return ['region'];
    }

    /**
     * @inheritDoc
     */
    public function getRegionAttributes(RegionInterface $region): array
    {
        return [
            'id' => 'region-' . $region->handle,
            'data-handle' => $region->handle
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFieldClasses(FieldInterface $field): array
    {
        return ['field-content'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldAttributes(FieldInterface $field): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getFieldContainerClasses(FieldInterface $field): array
    {
        return ['display', 'field'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldContainerAttributes(FieldInterface $field): array
    {
        $attributes = [
            'data-displayer' => $field->displayer->handle,
            'data-field' => $field->handle
        ];
        if ($field->visuallyHidden) {
            $attributes['style'] = 'display:none';
        }
        return $attributes;
    }

    /**
     * @inheritDoc
     */
    public function getFieldLabelClasses(FieldInterface $field): array
    {
        return ['field-label'];
    }

    /**
     * @inheritDoc
     */
    public function getFieldLabelAttributes(FieldInterface $field): array
    {
        if ($field->labelVisuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getGroupClasses(GroupInterface $group): array
    {
        return ['group-content'];
    }

    /**
     * @inheritDoc
     */
    public function getGroupAttributes(GroupInterface $group): array
    {
        $attributes = [
            'data-handle' => $group->handle
        ];
        if ($group->visuallyHidden) {
            $attributes['style'] = 'display:none';
        }
        return $attributes;
    }

    /**
     * @inheritDoc
     */
    public function getGroupContainerClasses(GroupInterface $group): array
    {
        return ['display', 'group'];
    }

    /**
     * @inheritDoc
     */
    public function getGroupContainerAttributes(GroupInterface $group): array
    {
        if ($group->visuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getGroupLabelClasses(GroupInterface $group): array
    {
        return ['group-label'];
    }

    /**
     * @inheritDoc
     */
    public function getGroupLabelAttributes(GroupInterface $group): array
    {
        if ($group->labelVisuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getFileClasses(Asset $asset, FieldInterface $field, FileDisplayerInterface $displayer): array
    {
        return ['file'];
    }

    /**
     * @inheritDoc
     */
    public function getFileAttributes(Asset $asset, FieldInterface $field, FileDisplayerInterface $displayer): array
    {
        return [
            'data-displayer' => $displayer->handle,
            'data-field' => $field->handle
        ];
    }

    /**
     * View mode getter
     * 
     * @return ViewModeInterface
     */
    protected function getViewMode(): ViewModeInterface
    {
        return $this->view->renderingViewMode;
    }

    /**
     * Layout getter
     * 
     * @return LayoutInterface
     */
    protected function getLayout(): LayoutInterface
    {
        return $this->view->renderingLayout;
    }

    /**
     * Region getter
     * 
     * @return RegionInterface
     */
    protected function getRegion(): RegionInterface
    {
        return $this->view->renderingRegion;
    }

    /**
     * Element getter
     * 
     * @return ?Element
     */
    protected function getElement(): ?Element
    {
        return $this->view->renderingElement;
    }
}