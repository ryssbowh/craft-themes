<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Display;
use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\records\BlockRecord;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;
use craft\base\Model;
use craft\helpers\StringHelper;

class Layout extends Model
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
    public $element;

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
    protected $_loaded = false;

    /**
     * @var string|Entry|Category
     */
    protected $_element;

    protected $_displays;

    protected $_viewModes;

    protected $_blocks;

    public function defineRules(): array
    {
        return [
            [['type', 'theme'], 'required'],
            ['type', 'in', 'range' => LayoutService::TYPES],
            [['theme', 'element'], 'string'],
            ['hasBlocks', 'boolean'],
            [['dateCreated', 'dateUpdated', 'uid', 'id'], 'safe']
        ];
    }

    public function eagerLoadFields(Element $element, string $viewMode)
    {
        $with = [];
        foreach ($this->getVisibleDisplays($viewMode) as $display) {
            if ($fields = $display->item->displayer->eagerLoad()) {
                $with = array_merge($with, $fields);
            }
        }
        \Craft::$app->elements->eagerLoadElements(get_class($element), [$element], $with);
    }

    public function canHaveUrls(): bool
    {
        return true;
    }

    /**
     * Can this layout define displays
     * 
     * @return bool
     */
    public function hasDisplays(): bool
    {
        return false;
    }

    /**
     * Get theme object
     * 
     * @return ThemeInterface
     */
    public function getTheme(): ThemeInterface
    {
        if (!$this->theme) {
            throw LayoutException::noTheme();
        }
        return Themes::$plugin->registry->getTheme($this->theme);
    }

    /**
     * Get project config
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'theme' => $this->theme,
            'type' => $this->type,
            'handle' => $this->handle,
            'element' => $this->element,
            'hasBlocks' => $this->hasBlocks,
            'viewModes' => array_map(function ($viewMode) {
                return $viewMode->getConfig();
            }, $this->viewModes),
            'blocks' => array_map(function ($block) {
                return $block->getConfig();
            }, $this->blocks),
            'displays' => array_map(function ($display) {
                return $display->getConfig();
            }, $this->displays)
        ];
    }

    /**
     * Get description
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Default');
    }

    /**
     * Get element associated to that layout
     * 
     * @return mixed
     */
    public function element()
    {
        if ($this->_element == null) {
            $this->_element = $this->loadElement();
        }
        return $this->_element;
    }

    public function getViewModes(): array
    {
        if ($this->_viewModes === null) {
            $this->_viewModes = Themes::$plugin->viewModes->getForLayout($this);
        }
        return $this->_viewModes;
    }

    public function setViewModes(?array $viewModes): Layout
    {
        $this->_viewModes = $viewModes;
        return $this;
    }

    public function getBlocks(): array
    {
        if ($this->_blocks === null) {
            $this->_blocks = Themes::$plugin->blocks->getForLayout($this);
        }
        return $this->_blocks;
    }

    public function setBlocks(?array $blocks): Layout
    {
        $this->_blocks = $blocks;
        return $this;
    }

    public function addBlock(BlockInterface $block): Layout
    {
        if (!$this->_loaded) {
            $this->loadBlocks();
        }
        $this->_blocks[] = $block;
        $this->getRegion($block->region)->addBlock($block);
        return $this;
    }

    public function getElementMachineName(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['description', 'handle', 'viewModes']);
    }

    /**
     * Load blocks from database.
     * If this layout doesn't define blocks it will load its blocks from the default layout
     * 
     * @param  boolean $force
     * @return Layout
     */
    public function loadBlocks(bool $force = false): Layout
    {
        if ($this->_loaded and !$force) {
            return $this;
        }
        $this->regions = $this->getTheme()->getRegions();
        if (!$this->hasBlocks) {
            $default = Themes::$plugin->layouts->getDefault($this->theme);
            $this->blocks = Themes::$plugin->blocks->getForLayout($default);
        } else {
            $this->blocks = Themes::$plugin->blocks->getForLayout($this);
        }
        foreach ($this->blocks as $block) {
            $this->getRegion($block->region, false)->addBlock($block);
        }
        $this->_loaded = true;
        return $this;
    }

    public function getRegion(string $handle, bool $checkLoaded = true): Region
    {
        if ($checkLoaded and !$this->_loaded) {
            throw LayoutException::notLoaded($this, __METHOD__);
        }
        foreach ($this->regions as $region) {
            if ($region->handle == $handle) {
                return $region;
            }
        }
        if (!$this->_loaded) {
            throw LayoutException::noRegion($handle);
        }
    }

    public function findBlock(string $machineName): ?BlockInterface
    {
        if (!$this->_loaded) {
            throw LayoutException::notLoaded($this, __METHOD__);
        }
        foreach ($this->blocks as $block) {
            if ($block->getMachineName() == $machineName) {
                return $block;
            }
        }
        return null;
    }

    /**
     * Get all displays for a view mode
     * 
     * @return array
     */
    public function getDisplays(?string $viewMode = null): array
    {
        if (is_null($this->_displays)) {
            $this->_displays = Themes::$plugin->display->getForLayout($this);
        }
        if (is_null($viewMode)) {
            return $this->_displays;
        }
        return array_filter($this->_displays, function ($display) use ($viewMode) {
            return $viewMode == $display->viewMode->handle;
        });
    }

    public function setDisplays(array $displays)
    {
        $this->_displays = $displays;
    }

    public function findDisplayById(int $displayId): ?Display
    {
        foreach ($this->displays as $display) {
            if ($display->id == $displayId) {
                return $display;
            }
        }
        return null;
    }

    public function replaceDisplay(Display $display)
    {
        foreach ($this->displays as $i => $oldDisplay) {
            if ($oldDisplay->id == $display->id) {
                $this->_displays[$i] = $display;
            }
        }
    }

    /**
     * Get all visible displays
     * 
     * @return array
     */
    public function getVisibleDisplays(string $viewMode = ViewModeService::DEFAULT_HANDLE): array
    {
        return array_filter($this->getDisplays($viewMode), function ($display) {
            return $display->item->isVisible();
        });
    }

    public function getCraftFields(): array
    {
        return [];
    }

    /**
     * Load element
     */
    protected function loadElement()
    {
        return '';
    }

    /**
     * get handle
     * 
     * @return string
     */
    public function getHandle(): string
    {
        return StringHelper::camelCase($this->type . '_' . $this->theme);
    }

    public function render(Element $element, string $viewMode = ViewModeService::DEFAULT_HANDLE): string
    {
        return Themes::$plugin->view->renderLayout($this, $viewMode, $element);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function getRenderingMode(): string
    {
        return $this->_renderingMode;
    }

    public function setRegionsRenderingMode()
    {
        $this->_renderingMode = self::RENDERING_MODE_REGIONS;
    }

    public function setDisplaysRenderingMode()
    {
        $this->_renderingMode = self::RENDERING_MODE_DISPLAYS;
    }
}