<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\traits\HasDisplays;
use craft\base\Element;

/**
 * Class for a group of items
 */
class Group extends DisplayItem implements GroupInterface
{
    use HasDisplays;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

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
    public function getAllDisplays(): array
    {
        return $this->displays;
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(): array
    {
        $fields = [];
        foreach ($this->visibleDisplays as $display) {
            $fields = array_merge($fields, $display->item->eagerLoad());
        }
        return $fields;
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
    public function getTemplates(): array
    {
        $type = $this->layout->type;
        $key = $this->layout->templatingKey;
        $viewMode = $this->viewMode->handle;
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

    /**
     * @inheritDoc
     */
    protected function loadDisplays(): array
    {
        return Themes::$plugin->displays->getForGroup($this->id);
    }
}