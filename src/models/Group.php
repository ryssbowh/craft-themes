<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\services\DisplayService;
use craft\base\Element;

class Group extends DisplayItem
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
     * Displays getter
     * 
     * @return array
     */
    public function getDisplays(): array
    {
        if ($this->_displays == null) {
            $this->_displays = Themes::$plugin->displays->getForGroup($this->display->id);
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
     * Visible displays getter
     * 
     * @return array
     */
    public function getVisibleDisplays(): array
    {
        return array_filter($this->displays, function ($display) {
            return $display->item->isVisible();
        });
    }

    /**
     * Displays setter
     * 
     * @param array $displays
     */
    public function setDisplays(?array $displays)
    {
        if (is_array($displays)) {
            foreach ($displays as $display) {
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
    public function render(Element $element): string
    {
        return Themes::$plugin->view->renderGroup($this, $element);
    }
}