<?php
namespace Ryssbowh\CraftThemes\models;

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
use craft\base\Component;
use craft\elements\Asset;

class ThemePreferences extends Component implements ThemePreferencesInterface
{
    protected $view;

    public function init()
    {
        $this->view = Themes::$plugin->view;
    }

    public function getLayoutClasses(LayoutInterface $layout, bool $root = false): array
    {
        $classes = ['layout', 'layout-' . $layout->type, 'view-mode-' . $this->viewMode->handle];
        if ($class = $layout->elementMachineName) {
            $classes[] = 'handle-' . $class;
        }
        return $classes;
    }

    public function getLayoutAttributes(LayoutInterface $layout, bool $root = false): array
    {
        return [];
    }

    public function getBlockClasses(BlockInterface $block): array
    {
        return ['block', 'block-' . $block->getMachineName()];
    }

    public function getBlockAttributes(BlockInterface $block): array
    {
        return [];
    }

    public function getRegionClasses(RegionInterface $region): array
    {
        return ['region', 'region-' . $region->handle];
    }

    public function getRegionAttributes(RegionInterface $region): array
    {
        return ['id' => 'region-' . $region->handle];
    }

    public function getFieldClasses(FieldInterface $field): array
    {
        return ['field-content'];
    }

    public function getFieldAttributes(FieldInterface $field): array
    {
        return [];
    }

    public function getFieldContainerClasses(FieldInterface $field): array
    {
        return ['display', 'field', 'field-' . $field->handle, $field->displayer->handle];
    }

    public function getFieldContainerAttributes(FieldInterface $field): array
    {
        if ($field->visuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
    }

    public function getFieldLabelClasses(FieldInterface $field): array
    {
        return ['field-label'];
    }

    public function getFieldLabelAttributes(FieldInterface $field): array
    {
        if ($field->labelVisuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
    }

    public function getGroupClasses(GroupInterface $group): array
    {
        return ['group-content'];
    }

    public function getGroupAttributes(GroupInterface $group): array
    {
        if ($group->visuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
    }

    public function getGroupContainerClasses(GroupInterface $group): array
    {
        return ['display', 'group', 'group-' . $group->handle];
    }

    public function getGroupContainerAttributes(GroupInterface $group): array
    {
        if ($group->visuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
    }

    public function getGroupLabelClasses(GroupInterface $group): array
    {
        return ['group-label'];
    }

    public function getGroupLabelAttributes(GroupInterface $group): array
    {
        if ($group->labelVisuallyHidden) {
            return ['style' => 'display:none'];
        }
        return [];
    }

    public function getFileClasses(Asset $asset, FieldInterface $field, FileDisplayerInterface $displayer): array
    {
        return ['file', 'file-' . $displayer->handle];
    }

    public function getFileAttributes(Asset $asset, FieldInterface $field, FileDisplayerInterface $displayer): array
    {
        return [];
    }

    protected function getViewMode(): ViewModeInterface
    {
        return $this->view->renderingViewMode;
    }

    protected function getLayout(): LayoutInterface
    {
        return $this->view->renderingLayout;
    }

    protected function getRegion(): RegionInterface
    {
        return $this->view->renderingRegion;
    }

    protected function getElement(): ?Element
    {
        return $this->view->renderingElement;
    }
}