<?php
namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\User;

/**
 * Controller for various ajax actions
 */
class CpAjaxController extends Controller
{
    /**
     * @inheritDoc
     */
    public function beforeAction($action) 
    {
        $this->requirePermission('accessPlugin-themes');
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        return true;
    }

    /**
     * (Re)install all layouts
     * 
     * @return array
     */
    public function actionInstall(): array
    {
        $this->layouts->install(true);
        return [
            'message' => \Craft::t('themes', 'Themes data has been installed')
        ];
    }

    /**
     * @inheritDoc
     */
    public function afterAction($action, $result)
    {
        return $this->asJson($result);
    }

    /**
     * Return categories data for an array of ids
     * 
     * @return array
     */
    public function actionCategoriesData(): array
    {
        $id = $this->request->getRequiredParam('id');
        $theme = $this->request->getRequiredParam('theme');
        $id = is_array($id) ? $id : [$id];
        $data = [];
        foreach ($id as $id) {
            $category = Category::find()->id($id)->one();
            if (!$category) {
                continue;
            }
            $layout = Themes::$plugin->layouts->get($theme, LayoutService::CATEGORY_HANDLE, $category->group->uid);
            $data[] = [
                'id' => $category->id,
                'status' => $category->status,
                'title' => $category->title,
                'level' => $category->level,
                'viewModes' => array_map(function ($viewMode) {
                    return [
                        'uid' => $viewMode->uid,
                        'name' => $viewMode->name
                    ];
                }, $layout->viewModes)
            ];
        }
        return $data;
    }

    /**
     * Return assets data for an array of ids
     * 
     * @return array
     */
    public function actionAssetsData(): array
    {
        $id = $this->request->getRequiredParam('id');
        $theme = $this->request->getRequiredParam('theme');
        $id = is_array($id) ? $id : [$id];
        $data = [];
        foreach ($id as $id) {
            $asset = Asset::find()->id($id)->one();
            if (!$asset) {
                continue;
            }
            $layout = Themes::$plugin->layouts->get($theme, LayoutService::VOLUME_HANDLE, $asset->volume->uid);
            $asset->setTransform(['width' => 34, 'height' => 25]);
            $data[] = [
                'id' => $asset->id,
                'title' => $asset->title,
                'srcset' => $asset->getSrcset(['34w', '68w']),
                'viewModes' => array_map(function ($viewMode) {
                    return [
                        'uid' => $viewMode->uid,
                        'name' => $viewMode->name
                    ];
                }, $layout->viewModes)
            ];
        }
        return $data;
    }

    /**
     * Return entries data for an array of ids
     * 
     * @return array
     */
    public function actionEntriesData(): array
    {
        $id = $this->request->getRequiredParam('id');
        $theme = $this->request->getRequiredParam('theme');
        $id = is_array($id) ? $id : [$id];
        $data = [];
        foreach ($id as $id) {
            $entry = Entry::find()->id($id)->one();
            if (!$entry) {
                continue;
            }
            $layout = Themes::$plugin->layouts->get($theme, LayoutService::ENTRY_HANDLE, $entry->type->uid);
            $data[] = [
                'id' => $entry->id,
                'status' => $entry->status,
                'title' => $entry->title,
                'viewModes' => array_map(function ($viewMode) {
                    return [
                        'uid' => $viewMode->uid,
                        'name' => $viewMode->name
                    ];
                }, $layout->viewModes)
            ];
        }
        return $data;
    }

    /**
     * Return users data for an array of ids
     * 
     * @return array
     */
    public function actionUsersData(): array
    {
        $id = $this->request->getRequiredParam('id');
        $theme = $this->request->getRequiredParam('theme');
        $id = is_array($id) ? $id : [$id];
        $data = [];
        $layout = Themes::$plugin->layouts->get($theme, LayoutService::USER_HANDLE);
        $viewModes = array_map(function ($viewMode) {
            return [
                'uid' => $viewMode->uid,
                'name' => $viewMode->name
            ];
        }, $layout->viewModes);
        foreach ($id as $id) {
            $user = User::find()->id($id)->one();
            if (!$user) {
                continue;
            }
            $name = $user->username;
            if ($user->firstName or $user->lastName) {
                $name = $user->firstName . ' ' . $user->lastName;
            }
            $data[] = [
                'id' => $user->id,
                'status' => $user->status,
                'name' => $name,
                'srcset' => $user->getThumbUrl(34) . ' 34w, ' . $user->getThumbUrl(68) . ' 68w',
                'viewModes' => $viewModes
            ];
        }
        return $data;
    }
}