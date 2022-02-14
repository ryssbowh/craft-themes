<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\GlobalBlockOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\GlobalSet;

/**
 * Block displaying a global set
 */
class GlobalBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'global';

    /**
     * @var GlobalSet
     */
    protected $_global = false;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'Global');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays a global set');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Choose a global set and a view mode to display');
    }

    /**
     * Get global set as defined in options
     * 
     * @return ?GlobalSet
     */
    public function getGlobalSet(): ?GlobalSet
    {
        if ($this->_global === false) {
            $this->_global = null;
            if ($this->viewMode) {
                $eagerLoadable = Themes::$plugin->eagerLoading->getEagerLoadable($this->viewMode);
                $this->_global = GlobalSet::find()->uid($this->options->set)->with($eagerLoadable)->one();;
            }
        }
        return $this->_global;
    }

    /**
     * Get the view mode as defined in the options
     * 
     * @return ?ViewModeInterface
     */
    public function getViewMode(): ?ViewModeInterface
    {
        try {
            return Themes::$plugin->viewModes->getByUid($this->options->viewMode);
        } catch (ViewModeException $e) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(): bool
    {
        return $this->globalSet != null;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return GlobalBlockOptions::class;
    }
}
