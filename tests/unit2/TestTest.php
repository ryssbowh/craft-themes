<?php

use Codeception\Test\Unit;
use Craft;
use UnitTester;
use craft\db\Query;
use craft\db\Table;
use craft\test\TestSetup;

class TestTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testDelete()
    {
        $group = \Craft::$app->categories->getGroupByHandle('category');
        \Craft::$app->categories->deleteGroup($group);
        \Craft::$app->getDb()->createCommand()
            ->update(Table::CATEGORYGROUPS, ['dateDeleted' => null], ['uid' => $group->uid])
            ->execute();
    }

    public function testExists()
    {
        dd(\Craft::$app->categories->getGroupByHandle('category'));
    }
}
