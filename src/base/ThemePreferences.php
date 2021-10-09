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
        $classes = ['layout', 'layout-' . $layout->type, 'view-mode-' . $this->viewMode->handle];
        if ($class = $layout->elementMachineName) {
            $classes[] = 'handle-' . $class;
        }
        return $classes;
    }

    /**
     * @inheritDoc
     */
    public function getLayoutAttributes(LayoutInterface $layout, bool $root = false): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getBlockClasses(BlockInterface $block): array
    {
        return ['block', 'block-' . $block->getMachineName()];
    }

    /**
     * @inheritDoc
     */
    public function getBlockAttributes(BlockInterface $block): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getRegionClasses(RegionInterface $region): array
    {
        return ['region', 'region-' . $region->handle];
    }

    /**
     * @inheritDoc
     */
    public function getRegionAttributes(RegionInterface $region): array
    {
        return ['id' => 'region-' . $region->handle];
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
        return ['display', 'field', 'field-' . $field->handle, $field->displayer->handle];
    }

    /**
     * @inheritDoc
     */
    public function getFieldContainerAttributes(FieldInterface $field): array
    {
        if ($field->visuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
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
        if ($group->visuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getGroupContainerClasses(GroupInterface $group): array
    {
        return ['display', 'group', 'group-' . $group->handle];
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
        return ['file', 'file-' . $displayer->handle];
    }

    /**
     * @inheritDoc
     */
    public function getFileAttributes(Asset $asset, FieldInterface $field, FileDisplayerInterface $displayer): array
    {
        return [];
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