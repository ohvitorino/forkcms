<?php

namespace Backend\Modules\Groups\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Engine\Model;
use Common\WebTestCase;

class ModelTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }

        $client = self::createClient();
        $this->loadFixtures($client);
    }

    public function testAddActionPermissions(): void
    {
    }

    public function testAddModulePermissions(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testAlreadyExists(): void
    {
        $this->assertTrue(Model::alreadyExists('admin'));
        $this->assertFalse(Model::alreadyExists('test'));
    }

    public function testDelete(): void
    {
        $existingGroupItem = BackendModel::getContainer()->get('database')->getRecord(
            'SELECT * FROM `groups` WHERE id = ?',
            [1]
        );

        Model::delete($existingGroupItem['id']);

        $existingGroupItems = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM `groups` WHERE id = ?',
            [1]
        );

        $this->assertNull($existingGroupItems);
    }

    public function testDeleteActionPermissions(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testDeleteModulePermissions(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testDeleteMultipleGroups(): void
    {
        $groupId = BackendModel::getContainer()->get('database')->insert(
            'groups',
            ['name' => 'test', 'parameters' => null]
        );
        BackendModel::getContainer()->get('database')->insert('users_groups', ['group_id' => $groupId, 'user_id' => 1]);

        Model::deleteMultipleGroups(1);

        $records = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM users_groups WHERE user_id = ?',
            [1]
        );

        $this->assertNull($records);
    }

    public function testExists(): void
    {
        $this->assertTrue(Model::exists('1'));
    }

    public function testExistsActionPermission(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testExistsModulePermission(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testGet(): void
    {
        $group = Model::get(1);

        $this->assertEquals('admin', $group['name']);
        $this->assertNull($group['parameters']);

        $group = Model::get(2);

        $this->assertEquals('pages user', $group['name']);
        $this->assertNull($group['parameters']);
    }

    public function testGetActionPermissions(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testGetAll(): void
    {
        $groups = Model::getAll();

        $this->assertCount(3, $groups);

        $this->assertEquals('admin', $groups[0]['label']);
        $this->assertEquals('pages user', $groups[1]['label']);
        $this->assertEquals('users user', $groups[2]['label']);
    }

    public function testGetGroupsByUser(): void
    {
        $user = BackendModel::getContainer()->get('database')->getRecord('SELECT * FROM `users` LIMIT 1');
        $userGroups = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT group_id FROM `users_groups` WHERE user_id = ?',
            [$user['id']]
        );

        $groups = Model::getGroupsByUser($user['id']);

        $this->assertCount(1, $groups);
        $this->assertEquals($userGroups[0]['group_id'], $groups[0]['id']);
    }

    public function testIsUserInGroup(): void
    {
        $this->assertTrue(Model::isUserInGroup(1, 1));
        $this->assertFalse(Model::isUserInGroup(1, 2));
    }

    public function testGetModulePermissions(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testGetSetting(): void
    {
        $setting = Model::getSetting('1', 'dashboard_sequence');
        $this->assertNotNull($setting);
    }

    public function testGetUsers(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testInsert(): void
    {
        Model::insert(
            [
                'name' => 'test',
                'parameters' => null,
            ],
            [
                'name' => 'setting_test',
                'value' => serialize(null),
            ]
        );

        $insertedGroupItem = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM `groups` WHERE name = ?',
            ['test']
        );

        $this->assertCount(1, $insertedGroupItem);
        $this->assertEquals('test', $insertedGroupItem[0]['name']);
        $this->assertNull($insertedGroupItem[0]['parameters']);

        $insertedSettingItem = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM `groups_settings` WHERE group_id = ?',
            [$insertedGroupItem[0]['id']]
        );

        $this->assertCount(1, $insertedSettingItem);
        $this->assertEquals('setting_test', $insertedSettingItem[0]['name']);
        $this->assertNull(unserialize($insertedSettingItem[0]['value']));
    }

    public function testInsertMultipleGroups(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testInsertSetting(): void
    {
        Model::insertSetting(['group_id' => 1, 'name' => 'test', 'value' => serialize(null)]);

        $insertedSettingItems = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM `groups_settings` WHERE name = ?',
            ['test']
        );

        $this->assertCount(1, $insertedSettingItems);
        $this->assertEquals(1, $insertedSettingItems[0]['group_id']);
        $this->assertEquals('test', $insertedSettingItems[0]['name']);
        $this->assertEquals(null, unserialize($insertedSettingItems[0]['value']));
    }

    public function testUpdate(): void
    {
        $existingGroupItem = BackendModel::getContainer()->get('database')->getRecord(
            'SELECT * FROM `groups` WHERE id = ?',
            [1]
        );
        $existingSetting = BackendModel::getContainer()->get('database')->getRecord(
            'SELECT * FROM `groups_settings` WHERE group_id = ?',
            [1]
        );

        $existingGroupItem['name'] = 'test2';

        Model::update($existingGroupItem, $existingSetting);

        $modifiedGroupItem = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM `groups` WHERE id = ?',
            [1]
        );
        $modifiedSettingItem = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM `groups_settings` WHERE group_id = ?',
            [1]
        );

        $this->assertCount(1, $modifiedGroupItem);
        $this->assertCount(1, $modifiedSettingItem);
        $this->assertEquals('test2', $modifiedGroupItem[0]['name']);
        $this->assertEquals($existingGroupItem['parameters'], $modifiedGroupItem[0]['parameters']);
        $this->assertEquals($existingSetting['name'], $modifiedSettingItem[0]['name']);
    }

    public function testUpdateSetting(): void
    {
        $setting = BackendModel::getContainer()->get('database')->getRecord(
            'SELECT * FROM `groups_settings` WHERE name = ?',
            ['dashboard_sequence']
        );

        $setting['value'] = serialize([]);

        Model::updateSetting($setting);

        $modifiedSettingItems = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM `groups_settings` WHERE name = ?',
            ['dashboard_sequence']
        );

        $this->assertCount(1, $modifiedSettingItems);
        $this->assertEquals(1, $modifiedSettingItems[0]['group_id']);
        $this->assertEquals('dashboard_sequence', $modifiedSettingItems[0]['name']);
        $this->assertEquals([], unserialize($modifiedSettingItems[0]['value']));
    }
}
