<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\records\BlockRecord;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;
use craft\base\Model;
use craft\helpers\StringHelper;

class Layout extends Model implements LayoutInterface
{
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
    public $themeHandle;

    /**
     * @var string
     */
    public $elementUid;

    /**
     * @var boolean
     */
    public $hasBlocks = false;

    /**
     * @var string
     */
    public $uid;

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
    protected $_regions;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['type', 'themeHandle'], 'required'],
            ['type', 'in', 'range' => LayoutService::TYPES],
            [['themeHandle', 'elementUid'], 'string'],
            ['hasBlocks', 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['uid', 'id', 'element'], 'safe'],
            ['themeHandle', function () {
                if (!Themes::$plugin->registry->hasTheme($this->themeHandle)) {
                    $this->addError('themeHandle', \Craft::t('themes', 'Theme ' . $this->themeHandle . ' doesn\'t exist'));
                } else {
                    if ($this->theme->isPartial()) {
                        $this->addError('themeHandle', \Craft::t('themes', 'Layouts can\'t be added to partial themes'));
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
        $viewMode = $this->getViewMode($viewMode);
        foreach ($viewMode->getVisibleDisplays() as $display) {
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
    public function getRegions(): array
    {
        if (is_null($this->_regions)) {
            $this->_regions = [];
            foreach ($this->theme->regions as $config) {
                $config['layout'] = $this;
                $this->_regions[$config['handle']] = new Region($config);
            }
        }
        return $this->_regions;
    }

    /**
     * @inheritDoc
     */
    public function getTheme(): ThemeInterface
    {
        if (!$this->themeHandle) {
            throw LayoutException::noTheme($this);
        }
        return Themes::$plugin->registry->getTheme($this->themeHandle);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'themeHandle' => $this->themeHandle,
            'type' => $this->type,
            'elementUid' => $this->elementUid,
            'hasBlocks' => $this->hasBlocks,
            'uid' => $this->uid ?? StringHelper::UUID()
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
    public function getDefaultViewMode(): ViewModeInterface
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
    public function getViewMode(string $handle): ?ViewModeInterface
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
        if (is_array($viewModes)) {
            foreach ($viewModes as $viewMode) {
                $viewMode->layout = $this;
            }
        }
        $this->_viewModes = $viewModes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addViewMode(ViewModeInterface $viewMode): LayoutInterface
    {
        $viewMode->layout = $this;
        $viewModes = $this->viewModes;
        $viewModes[] = $viewMode;
        $this->viewModes = $viewModes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasViewMode(string $handle): bool
    {
        return !is_null($this->getViewMode($handle));
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
        return array_merge(parent::fields(), ['description']);
    }

    /**
     * @inheritDoc
     */
    public function getBlocks(): array
    {
        $blocks = array_map(function ($region) {
            return $region->blocks;
        }, $this->regions);
        return array_merge(...array_values($blocks));
    }

    /**
     * @inheritDoc
     */
    public function setBlocks(array $blocks): LayoutInterface
    {
        foreach ($this->regions as $region) {
            $region->blocks = [];
        }
        foreach ($blocks as $block) {
            $this->addBlock($block, $block->region);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBlock(BlockInterface $block, string $region): LayoutInterface
    {
        $this->getRegion($region)->addBlock($block);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRegion(string $handle): Region
    {
        if (isset($this->regions[$handle])) {
            return $this->regions[$handle];
        }
        throw ThemeException::noRegion($this->themeHandle, $handle);
    }

    /**
     * @inheritDoc
     */
    public function hasRegion(string $handle): bool
    {
        return isset($this->regions[$handle]);
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
    public function replaceDisplay(DisplayInterface $display): LayoutInterface
    {
        foreach ($this->displays as $i => $oldDisplay) {
            if ($oldDisplay->id == $display->id) {
                $this->_displays[$i] = $display;
            }
        }
        return $this;
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
     * Loads this layout associated element (category group, entry type etc)
     * 
     * @return mixed
     */
    protected function loadElement()
    {
        return '';
    }
}