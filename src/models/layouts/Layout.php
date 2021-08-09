<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\records\BlockRecord;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;
use craft\base\Model;
use craft\helpers\StringHelper;

class Layout extends Model implements LayoutInterface
{
    const RENDERING_MODE_REGIONS = 'regions';
    const RENDERING_MODE_DISPLAYS = 'displays';

    /**
     * @var array
     */
    public $regions = [];

    /**
     * @var id
     */
    public $id;

    /**
     * @var string
     */
    public $type = LayoutService::DEFAULT_HANDLE;

    /**
     * @var int
     */
    public $theme;

    /**
     * @var string
     */
    public $elementUid;

    /**
     * @var boolean
     */
    public $hasBlocks = 0;

    /**
     * @var DateTime
     */
    public $dateCreated;

    /**
     * @var DateTime
     */
    public $dateUpdated;

    /**
     * @var string
     */
    public $uid;

    /**
     * Rendering mode, 'regions' or 'fields'
     * @var string
     */
    protected $_renderingMode = self::RENDERING_MODE_REGIONS;

    /**
     * @var boolean
     */
    protected $_blocksLoaded = false;

    /**
     * @var boolean
     */
    protected $_regionsLoaded = false;

    /**
     * Element associated with this layout (entry type, user, category group etc)
     * @var mixed
     */
    protected $_element;

    /**
     * @var array
     */
    protected $_displays;

    /**
     * @var array
     */
    protected $_viewModes;

    /**
     * @var array
     */
    protected $_blocks;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['type', 'theme'], 'required'],
            ['type', 'in', 'range' => LayoutService::TYPES],
            [['theme', 'elementUid'], 'string'],
            ['hasBlocks', 'boolean'],
            [['dateCreated', 'dateUpdated', 'uid', 'id', 'element'], 'safe'],
            ['theme', function () {
                if (!Themes::$plugin->registry->hasTheme($this->theme)) {
                    $this->addError('theme', \Craft::t('themes', 'Theme ' . $this->theme . ' doesn\'t exist'));
                } else {
                    $theme = Themes::$plugin->registry->getTheme($this->theme);
                    if ($theme->isPartial()) {
                        $this->addError('theme', \Craft::t('themes', 'Layouts can\'t be added to partial themes'));
                    }
                }
            }]
        ];
    }

    /**
     * @inheritDoc
     */
    public function eagerLoadFields(Element $element, string $viewMode)
    {
        $with = [];
        foreach ($this->getVisibleDisplays($viewMode) as $display) {
            $with = array_merge($with, $display->item->eagerLoad());
        }
        \Craft::$app->elements->eagerLoadElements(get_class($element), [$element], $with);
    }

    /**
     * @inheritDoc
     */
    public function canHaveUrls(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function hasDisplays(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getTheme(): ThemeInterface
    {
        if (!$this->theme) {
            throw LayoutException::noTheme();
        }
        return Themes::$plugin->registry->getTheme($this->theme);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'theme' => $this->theme,
            'type' => $this->type,
            'elementUid' => $this->elementUid,
            'hasBlocks' => $this->hasBlocks
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Default');
    }

    /**
     * @inheritDoc
     */
    public function getElement()
    {
        if ($this->_element == null) {
            $this->_element = $this->loadElement();
        }
        return $this->_element;
    }

    /**
     * @inheritDoc
     */
    public function setElement($element)
    {
        $this->_element = $element;
    }

    /**
     * @inheritDoc
     */
    public function getViewModes(): array
    {
        if ($this->_viewModes === null) {
            $this->_viewModes = Themes::$plugin->viewModes->getForLayout($this);
        }
        return $this->_viewModes;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultViewMode(): ViewMode
    {
        foreach ($this->viewModes as $viewMode) {
            if ($viewMode->handle == ViewModeService::DEFAULT_HANDLE) {
                return $viewMode;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getViewModeByHandle(string $handle): ?ViewMode
    {
        foreach ($this->viewModes as $viewMode) {
            if ($viewMode->handle == $handle) {
                return $viewMode;
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setViewModes(?array $viewModes): LayoutInterface
    {
        $this->_viewModes = $viewModes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBlocks(): array
    {
        if ($this->_blocks === null) {
            $this->_blocks = Themes::$plugin->blocks->getForLayout($this);
        }
        return $this->_blocks;
    }

    /**
     * @inheritDoc
     */
    public function setBlocks(?array $blocks): LayoutInterface
    {
        $this->_blocks = $blocks;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBlock(BlockInterface $block): LayoutInterface
    {
        $this->loadBlocks();
        $this->_blocks[] = $block;
        $block->layout = $this;
        $this->getRegion($block->region)->addBlock($block);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getElementMachineName(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['description', 'viewModes']);
    }

    /**
     * @inheritDoc
     */
    public function loadBlocks(bool $force = false): LayoutInterface
    {
        if ($this->_blocksLoaded and !$force) {
            return $this;
        }
        $this->loadRegions();
        if (!$this->hasBlocks) {
            $default = Themes::$plugin->layouts->getDefault($this->theme);
            $this->blocks = Themes::$plugin->blocks->getForLayout($default);
        } else {
            $this->blocks = Themes::$plugin->blocks->getForLayout($this);
        }
        foreach ($this->blocks as $block) {
            $this->getRegion($block->region)->addBlock($block);
        }
        $this->_blocksLoaded = true;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function loadRegions(): LayoutInterface
    {
        if ($this->_regionsLoaded) {
            return $this;
        }
        $this->regions = $this->getTheme()->getRegions();
        $this->_regionsLoaded = true;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRegion(string $handle): Region
    {
        $this->loadRegions();
        foreach ($this->regions as $region) {
            if ($region->handle == $handle) {
                return $region;
            }
        }
        throw LayoutException::noRegion($handle);
    }

    /**
     * @inheritDoc
     */
    public function findBlock(string $machineName): ?BlockInterface
    {
        $this->loadBlocks();
        foreach ($this->blocks as $block) {
            if ($block->getMachineName() == $machineName) {
                return $block;
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getDisplays(?string $viewMode = null): array
    {
        if (is_null($this->_displays)) {
            $this->_displays = Themes::$plugin->displays->getForLayout($this);
        }
        if (is_null($viewMode)) {
            return $this->_displays;
        }
        return array_filter($this->_displays, function ($display) use ($viewMode) {
            return $viewMode == $display->viewMode->handle;
        });
    }

    /**
     * @inheritDoc
     */
    public function getDisplayByHandle(string $viewMode, string $handle): ?DisplayInterface
    {
        foreach ($this->getDisplays($viewMode) as $display) {
            if ($display->item->handle == $handle) {
                return $display;
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setDisplays(array $displays)
    {
        $this->_displays = $displays;
    }

    /**
     * @inheritDoc
     */
    public function replaceDisplay(DisplayInterface $display)
    {
        foreach ($this->displays as $i => $oldDisplay) {
            if ($oldDisplay->id == $display->id) {
                $this->_displays[$i] = $display;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getVisibleDisplays(string $viewMode): array
    {
        if (!$this->hasViewMode($viewMode)) {
            throw LayoutException::noViewMode($viewMode);
        }
        return array_filter($this->getDisplays($viewMode), function ($display) {
            return $display->group_id === null and $display->item->isVisible();
        });
    }

    /**
     * @inheritDoc
     */
    public function hasViewMode(string $viewMode): bool
    {
        foreach ($this->viewModes as $mode) {
            if ($mode->handle == $viewMode) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getCraftFields(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function render(Element $element, string $viewMode = ViewModeService::DEFAULT_HANDLE): string
    {
        return Themes::$plugin->view->renderLayout($this, $viewMode, $element);
    }

    /**
     * @inheritDoc
     */
    public function getRenderingMode(): string
    {
        return $this->_renderingMode;
    }

    /**
     * @inheritDoc
     */
    public function setRegionsRenderingMode()
    {
        $this->_renderingMode = self::RENDERING_MODE_REGIONS;
    }

    /**
     * @inheritDoc
     */
    public function setDisplaysRenderingMode()
    {
        $this->_renderingMode = self::RENDERING_MODE_DISPLAYS;
    }

    /**
     * Loads this layout associated element
     * 
     * @return mixed
     */
    protected function loadElement()
    {
        return '';
    }
}