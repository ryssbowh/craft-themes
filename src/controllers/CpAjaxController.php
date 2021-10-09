<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\services\LayoutService;
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
        $this->layouts->install();
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
     * Return entries for an entry type uid
     * 
     * @param  string $uid
     * @return array
     */
    public function actionEntries(string $uid): array
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

    /**
     * Return categories for a category group uid
     * 
     * @param  string $uid
     * @return array
     */
    public function actionCategories(string $uid): array
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

    /**
     * Return users
     * 
     * @return array
     */
    public function actionUsers(): array
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