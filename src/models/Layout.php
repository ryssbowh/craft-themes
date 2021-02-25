<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\records\BlockRecord;
use craft\base\Model;

class Layout extends Model
{
    /**
     * @var array
     */
    public $blocks = [];

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
    public $type = 'default';

    /**
     * @var int
     */
    public $theme;

    /**
     * @var string
     */
    public $element;

    /**
     * @var bool
     */
    public $default_entry;

    /**
     * @var bool
     */
    public $default_category;

    /**
     * @var bool
     */
    public $default_route;

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
     * @var boolean
     */
    protected $loaded = false;

    /**
     * @var string|Entry|Category
     */
    protected $_element;

    /**
     * @inheritDoc
     */
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

    /**
     * Create a layout
     * 
     * @param  array  $args
     * @return Layout
     * @throws LayoutException
     */
    public static function create(array $args): Layout
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
            'element' => $this->element,
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
     * Get element assoicated to that layout, could be en entry
     * a category, a route string definition, or nothing for the default layout
     * 
     * @return string|Entry|Category
     */
    public function getElement()
    {
        if ($this->_element == null) {
            $this->loadElement();
        }
        return $this->_element;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['description', 'handle']);
    }

    /**
     * Load blocks from database
     * 
     * @param  boolean $force
     * @return Layout
     */
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

    /**
     * Load element
     */
    protected function loadElement()
    {
        $this->_element = '';
    }

    /**
     * get handle
     * 
     * @return string
     */
    public function getHandle(): string
    {
        return 'default';
    }
}