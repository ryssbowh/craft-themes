<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\services\DisplayService;
use craft\base\Element;

/**
 * Class for a group of items
 */
class Group extends DisplayItem implements GroupInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $handle;

    /**
     * @var array
     */
    protected $_displays;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['name', 'handle'], 'string'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return DisplayService::TYPE_GROUP;
    }

    /**
     * @inheritDoc
     */
    public function getDisplays(): array
    {
        if ($this->_displays == null) {
            $this->_displays = Themes::$plugin->displays->getForGroup($this->id);
        }
        return $this->_displays;
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(): array
    {
        $fields = [];
        foreach ($this->getVisibleDisplays() as $display) {
            $fields = array_merge($fields, $display->item->eagerLoad());
        }
        return $fields;
    }

    /**
     * @inheritDoc
     */
    public function getVisibleDisplays(): array
    {
        return array_filter($this->displays, function ($display) {
            return $display->item->isVisible();
        });
    }

    /**
     * @inheritDoc
     */
    public function setDisplays(?array $displays)
    {
        if (is_array($displays)) {
            foreach ($displays as $display) {
                $display->viewMode = null;
                $display->viewMode_id = null;
                $display->group = $this;
            }
        }
        $this->_displays = $displays;
    }

    /**
     * @inheritDoc
     */
    public function isVisible(): bool
    {
        return !$this->hidden;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return array_merge(parent::getConfig(), [
            'name' => $this->name,
            'handle' => $this->handle
        ]);
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['displays']);
    }

    /**
     * @inheritDoc
     */
    public function getTemplates(LayoutInterface $layout, ViewModeInterface $viewMode): array
    {
        $type = $layout->type;
        $key = $layout->getTemplatingKey();
        $viewMode = $viewMode->handle;
        return [
            'groups/' . $type . '/' . $key . '/' . $viewMode . '/group-' . $this->handle,
            'groups/' . $type . '/' . $key . '/' . $viewMode . '/group',
            'groups/' . $type . '/' . $key . '/group-' . $this->handle,
            'groups/' . $type . '/' . $key . '/group',
            'groups/' . $type . '/group-' . $this->handle,
            'groups/' . $type . '/group',
            'groups/group-' . $this->handle,
            'groups/group'
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(): bool
    {
        return sizeof($this->visibleDisplays) > 0;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return Themes::$plugin->view->renderGroup($this);
    }
}