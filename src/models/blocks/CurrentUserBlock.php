<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\CurrentUserBlockOptions;
use Ryssbowh\CraftThemes\services\LayoutService;

/**
 * Block displaying the current user
 */
class CurrentUserBlock extends Block
{
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
        return Themes::$plugin->layouts->get($this->layout->theme, LayoutService::USER_HANDLE, '');
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
