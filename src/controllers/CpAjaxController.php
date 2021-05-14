<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\User;

class CpAjaxController extends Controller
{
    public function beforeAction($action) 
    {
        $this->requirePermission('accessPlugin-themes');
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        return true;
    }

    public function afterAction($action, $result)
    {
        return $this->asJson($result);
    }

    public function actionEntries(string $uid)
    {
        $entryTypes = array_values(array_filter(\Craft::$app->sections->getAllEntryTypes(), function ($entryType) use ($uid) {
            return $uid == $entryType->uid;
        }));
        $entries = array_map(function ($entry) {
            return [
                'uid' => $entry->uid,
                'title' => $entry->title
            ];
        }, Entry::find()->type($entryTypes[0])->all());
        usort($entries, function ($a, $b) {
            return ($a['title'] < $b['title']) ? -1 : 1;
        });
        return [
            'entries' => $entries
        ];
    }

    public function actionCategories(string $uid)
    {
        $group = \Craft::$app->categories->getGroupByUid($uid);
        $categories = array_map(function ($category) {
            return [
                'uid' => $category->uid,
                'title' => $category->title
            ];
        }, Category::find()->group($group)->all());
        usort($categories, function ($a, $b) {
            return ($a['title'] < $b['title']) ? -1 : 1;
        });
        return [
            'categories' => $categories
        ];
    }

    public function actionUsers()
    {
        $users = array_map(function ($user) {
            return [
                'uid' => $user->uid,
                'title' => $user->friendlyName
            ];
        }, User::find()->all());
        usort($users, function ($a, $b) {
            return ($a['title'] < $b['title']) ? -1 : 1;
        });
        return [
            'users' => $users
        ];
    }
}