<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\ShortcutsAssets;
use Ryssbowh\CraftThemes\events\RenderEvent;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\GlobalSet;
use craft\elements\User;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use craft\web\View;
use yii\base\Application;
use yii\base\Event;

class ShortcutsService extends Service
{   
    /**
     * @var string
     */
    protected $js;

    /**
     * @var boolean
     */
    protected $inited = false;

    /**
     * @var boolean
     */
    protected $_showShortcuts;

    /**
     * Registers a layout
     * 
     * @param RenderEvent $e
     */
    public function registerLayout(RenderEvent $e) {
        
        if (!$this->showShortcuts) {
            return;
        }
        $this->initShortcuts();
        $layout = $e->variables['layout'];
        $element = $e->variables['element'];
        $viewMode = $e->variables['viewMode'];
        $theme = $this->themesRegistry()->current->handle;
        $id = StringHelper::UUID();
        $renderingRegions = (Themes::$plugin->view->renderingMode == LayoutInterface::RENDER_MODE_REGIONS);
        $e->variables['attributes']->add([
            'data-layout-shortcut' => $id
        ]);
        $js = "shortcutData['$id'] = [";
        if (\Craft::$app->user->checkPermission('accessCp')) {
            if ($element instanceof Entry) {
                $perm = 'editEntries:' . $layout->elementUid;
                if (\Craft::$app->user->checkPermission($perm)) {
                    $js .= "{
                        url: '" . $element->getCpEditUrl() . "',
                        label: '" . \Craft::t('themes', 'Edit Entry') . "',
                    },";
                }
            }
            if ($element instanceof Category) {
                $perm = 'editCategories:' . $layout->elementUid;
                if (\Craft::$app->user->checkPermission($perm)) {
                    $js .= "{
                        url: '" . $element->getCpEditUrl() . "',
                        label: '" . \Craft::t('themes', 'Edit Category') . "',
                    },";
                }
            }
            if ($element instanceof User) {
                if (\Craft::$app->user->checkPermission('editUsers')) {
                    $js .= "{
                        url: '" . $element->getCpEditUrl() . "',
                        label: '" . \Craft::t('themes', 'Edit User') . "',
                    },";
                }
            }
            if ($element instanceof GlobalSet) {
                $perm = 'editGlobalSet:' . $layout->elementUid;
                if (\Craft::$app->user->checkPermission($perm)) {
                    $js .= "{
                        url: '" . $element->getCpEditUrl() . "',
                        label: '" . \Craft::t('themes', 'Edit Global') . "',
                    },";
                }
            }
            if ($element instanceof Asset) {
                $perm = 'editImagesInVolume:' . $layout->elementUid;
                if (\Craft::$app->user->checkPermission($perm)) {
                    $js .= "{
                        url: '" . $element->getCpEditUrl() . "',
                        label: '" . \Craft::t('themes', 'Edit Asset') . "',
                    },";
                }
            }
            if ($renderingRegions and \Craft::$app->user->checkPermission('manageThemesBlocks') and \Craft::$app->config->getGeneral()->allowAdminChanges) {
                $js .= "{
                    url: '" . $layout->getEditBlocksUrl() . "',
                    label: '" . \Craft::t('themes', 'Edit Blocks') . "',
                },";
            }
            if ($layout->hasDisplays() and \Craft::$app->user->checkPermission('manageThemesDisplays') and \Craft::$app->config->getGeneral()->allowAdminChanges) {
                $js .= "{
                    url: '" . $layout->getEditDisplaysUrl($viewMode) . "',
                    label: '" . \Craft::t('themes', 'Edit Displays') . "',
                }";
            }
        }
        $this->js .= $js .'];';
        \Craft::$app->view->registerJs($this->js, View::POS_BEGIN, 'themes-shortcuts');
    }

    /**
     * Initialize the shortcuts
     */
    protected function initShortcuts()
    {
        if ($this->inited) {
            return;
        }
        \Craft::$app->view->registerAssetBundle(ShortcutsAssets::class);
        $this->js = "var shortcutData = {
        };";
        $this->inited = true;
    }

    /**
     * Should the shortcuts be shown ?
     * 
     * @return bool
     */
    protected function getShowShortcuts(): bool
    {
        if ($this->_showShortcuts === null) {
            $user = \Craft::$app->user->getIdentity();
            $showShortcuts = $user ? $user->getPreference('themesShowShorcuts', false) : false;
            $canViewShortcuts = \Craft::$app->user->checkPermission('viewThemesShortcuts');
            $this->_showShortcuts = (Themes::$plugin->is(Themes::EDITION_PRO) and $showShortcuts and $canViewShortcuts);
        }
        return $this->_showShortcuts;
    }
}