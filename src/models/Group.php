<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\traits\HasDisplays;
use Twig\Markup;
use craft\base\Element;

/**
 * Class for a group of displays
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
     * @var DisplayInterface[]
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
    public function eagerLoad(string $prefix = '', int $level = 0, array &$dependencies = []): array
    {
        $with = [];
        foreach ($this->visibleDisplays as $display) {
            $with = array_merge($with, $display->item->eagerLoad($prefix, $level, $dependencies));
        }
        return $with;
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
        return $this->layout->getGroupTemplates($this);
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
    public function render(): Markup
    {
        return Themes::$plugin->view->renderGroup($this);
    }

    /**
     * @inheritDoc
     */
    protected function loadDisplays(): array
    {
        if ($this->id) {
            return Themes::$plugin->displays->getForGroup($this->id);
        }
        return [];
    }
}