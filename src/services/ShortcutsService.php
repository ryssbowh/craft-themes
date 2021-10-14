<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\ShortcutsAssets;
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

    public function initShortcuts()
    {
        if ($this->inited) {
            return;
        }
        \Craft::$app->view->registerAssetBundle(ShortcutsAssets::class);
        $this->js = "var shortcutData = {
        };";
        $this->inited = true;
    }

    public function registerLayout($e) {
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
            if ($renderingRegions and \Craft::$app->user->checkPermission('manageThemesBlocks')) {
                if ($layout->hasBlocks) {
                    $url = UrlHelper::cpUrl('themes/blocks/' . $theme . '/' . $layout->id);
                } else {
                    $url = UrlHelper::cpUrl('themes/blocks/' . $theme . '/' . $this->layoutService()->getDefault($theme)->id);
                }
                $js .= "{
                    url: '" . $url . "',
                    label: '" . \Craft::t('themes', 'Edit Blocks') . "',
                },";
            }
            if ($layout->hasDisplays() and \Craft::$app->user->checkPermission('manageThemesDisplay')) {
                $js .= "{
                    url: '" . UrlHelper::cpUrl('themes/display/' . $theme . '/' . $layout->id) . '/' . $viewMode->handle . "',
                    label: '" . \Craft::t('themes', 'Edit Displays') . "',
                }";
            }
        }
        $this->js .= $js .'];';
        \Craft::$app->view->registerJs($this->js, View::POS_BEGIN, 'themes-shortcuts');
    }
}