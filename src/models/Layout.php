<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\records\BlockRecord;
use craft\base\Model;

class Layout extends Model
{
	public $blocks = [];
    public $regions = [];
	public $id;
    public $type = 'default';
    public $theme;
    public $element;
    public $default_entry;
    public $default_category;
    public $default_route;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    protected $loaded = false;
    protected $_element;

    public function init()
    {
        parent::init();
        if ($this->type === null) {
            throw LayoutException::noType();
        }
        if ($this->element === null) {
            throw LayoutException::noElement();
        }
    }

    public static function create(array $args)
    {
        if (!isset($args['type'])) {
            throw LayoutException::noType();
        }
        switch ($args['type']) {
            case 'default':
                return new Layout($args);
            case 'route':
                return new RouteLayout($args);
            case 'category':
                return new CategoryLayout($args);
            case 'entry':
                return new EntryLayout($args);
        }
        throw LayoutException::unknownType($args['type']);
    }

    public function getTheme(): ThemeInterface
    {
        if (!$this->theme) {
            throw LayoutException::noTheme();
        }
        return Themes::$plugin->registry->getTheme($this->theme);
    }

    public function getConfig(): array
    {
        return [
            'theme' => $this->theme,
            'type' => $this->type,
            'element' => $this->element,
        ];
    }

    public function getDescription(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getElement()
    {
        if ($this->_element == null) {
            $this->loadElement();
        }
        return $this->_element;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['description']);
    }

    public function loadBlocks($force = false): Layout
    {
        if ($this->loaded and !$force) {
            return $this;
        }
        $this->regions = $this->getTheme()->getRegions();
        $this->blocks = Themes::$plugin->blocks->getForLayout($this->theme, $this->id);
        foreach ($this->blocks as $block) {
            $this->regions[$block->region]->addBlock($block);
        }
        $this->loaded = true;
        return $this;
    }

    protected function loadElement()
    {
        $this->_element = '';
    }
}