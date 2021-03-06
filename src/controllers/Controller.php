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
     * @var FieldsService
     */
    protected $fields;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->registry = Themes::$plugin->registry;
        $this->blockProviders = Themes::$plugin->blockProviders;
        $this->blocks = Themes::$plugin->blocks;
        $this->layouts = Themes::$plugin->layouts;
        $this->viewModes = Themes::$plugin->viewModes;
        $this->fields = Themes::$plugin->fields;
    }
}