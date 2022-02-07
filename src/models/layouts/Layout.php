<?php
namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\RegionInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\records\BlockRecord;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;
use craft\base\Model;
use craft\helpers\UrlHelper;
use craft\models\FieldLayout;

/**
 * The default layout
 */
class Layout extends Model implements LayoutInterface
{
    /**
     * @var id
     */
    public $id;

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
     * Only used for custom layouts
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    protected $_type = LayoutService::DEFAULT_HANDLE;

    /**
     * Element associated with this layout (entry type, user, category group etc)
     * @var mixed
     */
    protected $_element;

    /**
     * @var ViewModeInterface[]
     */
    protected $_viewModes;

    /**
     * @var RegionInterface[]
     */
    protected $_regions;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['themeHandle'], 'required'],
            [['themeHandle', 'elementUid', 'name'], 'string'],
            ['hasBlocks', 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['uid', 'id', 'element'], 'safe'],
            ['themeHandle', 'validateThemeHandle'],
            ['elementUid', 'validateElementUid']
        ];
    }

    /**
     * Validates themeHandle attribute
     */
    public function validateThemeHandle()
    {
        if (!Themes::$plugin->registry->hasTheme($this->themeHandle)) {
            $this->addError('themeHandle', \Craft::t('themes', 'Theme ' . $this->themeHandle . ' doesn\'t exist'));
        } else {
            if ($this->theme->isPartial()) {
                $this->addError('themeHandle', \Craft::t('themes', 'Layouts can\'t be added to partial themes'));
            }
        }
    }

    /**
     * Validates elementUid attribute
     */
    public function validateElementUid()
    {
        if ($this->type == LayoutService::CUSTOM_HANDLE) {
            foreach (Themes::$plugin->layouts->all() as $layout) {
                if ($layout->id != $this->id and $layout->elementUid == $this->elementUid and $layout->themeHandle == $this->themeHandle) {
                    $this->addError('elementUid', \Craft::t('themes', 'Handle "' . $this->elementUid . '" already exists'));
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        foreach ($this->viewModes as $viewMode) {
            $viewMode->validate();
        }
        parent::afterValidate();
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);
        }
        foreach ($this->viewModes as $viewMode) {
            if ($viewMode->hasErrors()) {
                return true;
            }
        }
        return parent::hasErrors();
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        $errors = parent::getErrors();
        foreach ($this->viewModes as $index => $viewMode) {
            if ($viewMode->hasErrors()) {
                $errors['viewModes'][$index] = $viewMode->getErrors();
            }
        }
        if ($attribute === 'viewModes') {
            return $errors['viewModes'] ?? [];
        }
        if ($attribute !== null) {
            return parent::getErrors($attribute);
        }
        return $errors;
    }

    /**
     * Is custom getter
     * 
     * @return bool
     */
    public function getIsCustom(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * @inheritDoc
     */
    public function canHaveBlocks(): bool
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
            'name' => $this->name,
            'type' => $this->type,
            'elementUid' => $this->elementUid,
            'hasBlocks' => (bool)$this->hasBlocks
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
    public function getTemplatingKey(): string
    {
        return 'default';
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['type', 'description']);
    }

    /**
     * @inheritDoc
     */
    public function getBlocks(): array
    {
        $blocks = [];
        foreach ($this->regions as $region) {
            $blocks = array_merge($blocks, array_values($region->blocks));
        }
        return $blocks;
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
    public function getRegion(string $handle): RegionInterface
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
    public function getCraftFields(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function render(Element $element, $viewMode = ViewModeService::DEFAULT_HANDLE): string
    {
        return Themes::$plugin->view->renderLayout($this, $viewMode, $element);
    }

    /**
     * @inheritDoc
     */
    public function renderRegions(): string
    {
        return Themes::$plugin->view->renderLayout($this, ViewModeService::DEFAULT_HANDLE, null, LayoutInterface::RENDER_MODE_REGIONS);
    }

    /**
     * @inheritDoc
     */
    public function getTemplates(ViewModeInterface $viewMode): array
    {
        $key = $this->getTemplatingKey();
        $type = $this->type;
        return [
            'layouts/' . $type . '_' . $key . '_' . $viewMode->handle,
            'layouts/' . $type . '_' . $key,
            'layouts/' . $type,
            'layouts/layout',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getBlockTemplates(BlockInterface $block): array
    {
        $key = $this->getTemplatingKey();
        $type = $this->type;
        $name = $block->machineName;
        return [
            'blocks/' . $type . '_' . $key . '_' . $block->region . '_' . $name,
            'blocks/' . $type . '_' . $key . '_' . $name,
            'blocks/' . $type . '_' . $name,
            'blocks/' . $name,
            'blocks/block',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getRegionTemplates(RegionInterface $region): array
    {
        $handle = $region->handle;
        $type = $this->type;
        $key = $this->getTemplatingKey();
        return [
            'regions/' . $type . '_' . $key . '_region-' . $handle,
            'regions/' . $type . '_' . $key . '_region',
            'regions/' . $type . '_region-' . $handle,
            'regions/' . $type . '_region',
            'regions/region-' . $handle,
            'regions/region',
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getFieldTemplates(FieldInterface $field): array
    {
        $type = $this->type;
        $viewMode = $field->viewMode->handle;
        $key = $this->templatingKey;
        $displayer = $field->displayer->handle;
        $handle = $field->handle;
        return [
            'fields/' . $type . '_' . $key . '_' . $viewMode . '_' . $displayer . '-' . $handle,
            'fields/' . $type . '_' . $key . '_' . $viewMode . '_' . $displayer,
            'fields/' . $type . '_' . $key . '_' . $displayer . '-' . $handle,
            'fields/' . $type . '_' . $key . '_' . $displayer,
            'fields/' . $type . '_' . $displayer . '-' . $handle,
            'fields/' . $type . '_' . $displayer,
            'fields/' . $displayer . '-' . $handle,
            'fields/' . $displayer
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFileTemplates(FieldInterface $field, FileDisplayerInterface $displayer): array
    {
        $type = $this->type;
        $viewMode = $field->viewMode->handle;
        $key = $this->templatingKey;
        $displayer = $displayer->handle;
        $handle = $field->handle;
        return [
            'files/' . $type . '_' . $key . '_' . $viewMode . '_' . $displayer . '_' . $handle,
            'files/' . $type . '_' . $key . '_' . $viewMode . '_' . $displayer,
            'files/' . $type . '_' . $key . '_' . $displayer . '_' . $handle,
            'files/' . $type . '_' . $key . '_' . $displayer,
            'files/' . $type . '_' . $displayer . '_' . $handle,
            'files/' . $type . '_' . $displayer,
            'files/' . $displayer . '_' . $handle,
            'files/' . $displayer
        ];
    }

    /**
     * @inheritDoc
     */
    public function getGroupTemplates(GroupInterface $group): array
    {
        $type = $this->type;
        $key = $this->templatingKey;
        $viewMode = $group->viewMode->handle;
        $handle = $group->handle;
        return [
            'groups/' . $type . '_' . $key . '_' . $viewMode . '_group-' . $handle,
            'groups/' . $type . '_' . $key . '_' . $viewMode . '_group',
            'groups/' . $type . '_' . $key . '_group-' . $handle,
            'groups/' . $type . '_' . $key . '_group',
            'groups/' . $type . '_group-' . $handle,
            'groups/' . $type . '_group',
            'groups/group-' . $handle,
            'groups/group'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFieldLayout(): ?FieldLayout
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getEditDisplaysUrl($viewMode = null): ?string
    {
        if (!$this->hasDisplays()) {
            return null;
        }
        if (is_null($viewMode)) {
            $viewMode = $this->getDefaultViewMode();
        } elseif (is_string($viewMode)) {
            $viewMode = $this->getViewMode($viewMode);
        }
        return UrlHelper::cpUrl('themes/display/' . $this->theme->handle . '/' . $this->id) . '/' . $viewMode->handle;
    }

    /**
     * @inheritDoc
     */
    public function getEditBlocksUrl(): string
    {
        if (!$this->hasBlocks) {
            return Themes::$plugin->layouts->getDefault($this->theme)->getEditBlocksUrl();
        }
        return UrlHelper::cpUrl('themes/blocks/' . $this->theme->handle . '/' . $this->id);
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