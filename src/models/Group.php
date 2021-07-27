<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\records\GroupRecord;
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
     * Fields getter
     * 
     * @return array
     */
    public function getDisplays(): array
    {
        if ($this->_displays == null) {
            $this->_displays = Themes::$plugin->display->getForGroup($this->display->id);
        }
        return $this->_displays;
    }

    /**
     * Displays setter
     * 
     * @param array $displays
     */
    public function setDisplays(array $displays)
    {
        $this->_displays = $displays;
    }

    /**
     * @inheritDoc
     */
    public function isVisible(): bool
    {
        if ($this->hidden or !$this->displayer) {
            return false;
        }
        return true;
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
    public function render(Element $element): string
    {
        return Themes::$plugin->view->renderField($this, $element, $element->{$this->handle});
    }
}