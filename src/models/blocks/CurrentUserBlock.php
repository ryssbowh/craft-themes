<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\CurrentUserBlockOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

/**
 * Block displaying the current user
 */
class CurrentUserBlock extends Block
{
    /**
     * @var ViewModeInterface
     */
    protected $_viewMode = false;

    /**
     * @var string
     */
    public static $handle = 'current-user';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Current user');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the current user');
    }

    /**
     * Get layout associated to user defined in options
     * 
     * @return LayoutInterface
     */
    public function getUserLayout(): LayoutInterface
    {
        return Themes::$plugin->layouts->get($this->layout->theme, 'user', '');
    }

    /**
     * Get all view modes for user layout
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        $viewModes = [];
        foreach ($this->userLayout->viewModes as $viewMode) {
            $viewModes[$viewMode->uid] = $viewMode->name;
        }
        return $viewModes;
    }

    /**
     * Get view mode for current user and eager load it
     * 
     * @return ?ViewModeInterface
     */
    public function getViewMode(): ?ViewModeInterface
    {
        if ($this->_viewMode === false) {
            try {
                $user = \Craft::$app->user->getIdentity();
                if ($user) {
                    $this->_viewMode = Themes::$plugin->viewModes->getByUid($this->options->viewMode);
                    $eagerLoadable = Themes::$plugin->eagerLoading->getEagerLoadable($this->_viewMode);
                    \Craft::$app->elements->eagerLoadElements(get_class($user), [$user], $eagerLoadable);
                }
            } catch (ViewModeException $e) {
                return null;
            }
        }
        return $this->_viewMode;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(): bool
    {
        return \Craft::$app->user->getIdentity() != null;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return CurrentUserBlockOptions::class;
    }
}
