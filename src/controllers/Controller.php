<?php
namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use craft\web\Controller as CraftController;

class Controller extends CraftController
{
    /**
     * @var ThemesRegistry
     */
    protected $registry;

    /**
     * @var BlockProvidersService
     */
    protected $blockProviders;

    /**
     * @var BlockService
     */
    protected $blocks;

    /**
     * @var LayoutService
     */
    protected $layouts;

    /**
     * @var ViewModeService
     */
    protected $viewModes;

    /**
     * @var FieldDisplayerService
     */
    protected $fieldDisplayers;

    /**
     * @var DisplayService
     */
    protected $displays;

    /**
     * @var FieldsService
     */
    protected $fields;

    /**
     * @var BlockCacheService
     */
    protected $blockCache;

    /**
     * @var GroupsService
     */
    protected $groups;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->registry = Themes::$plugin->registry;
        $this->blockProviders = Themes::$plugin->blockProviders;
        $this->fieldDisplayers = Themes::$plugin->fieldDisplayers;
        $this->blocks = Themes::$plugin->blocks;
        $this->layouts = Themes::$plugin->layouts;
        $this->viewModes = Themes::$plugin->viewModes;
        $this->displays = Themes::$plugin->displays;
        $this->fields = Themes::$plugin->fields;
        $this->blockCache = Themes::$plugin->blockCache;
        $this->groups = Themes::$plugin->groups;
    }
}