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
        Model::addActionPermissions(
            [
                ['group_id' => 1, 'module' => 'test', 'action' => 'test_action', 'level' => 7],
            ]
        );

        $actionPermissions = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM groups_rights_actions WHERE group_id = ? AND module = ? AND action = ? AND level = ?',
            [1, 'test', 'test_action', 7]
        );

        $this->assertCount(1, $actionPermissions);
    }

    public function testAddModulePermissions(): void
    {
        Model::addModulePermissions(
            [
                ['group_id' => 1, 'module' => 'test'],
            ]
        );

        $modulePermissions = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM groups_rights_modules WHERE group_id = ? AND module = ?',
            [1, 'test']
        );

        $this->assertCount(1, $modulePermissions);
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
        BackendModel::getContainer()->get('database')->insert(
            'groups_rights_actions',
            ['group_id' => 1, 'module' => 'test', 'action' => 'test_action', 'level' => 7]
        );

        Model::deleteActionPermissions(
            [
                ['group_id' => 1, 'module' => 'test', 'action' => 'test_action', 'level' => 7],
            ]
        );

        $actionPermissions = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM groups_rights_actions WHERE group_id = ? AND module = ? AND action = ? AND level = ?',
            [1, 'test', 'test_action', 7]
        );

        $this->assertNull($actionPermissions);
    }

    public function testDeleteModulePermissions(): void
    {
        BackendModel::getContainer()->get('database')->insert(
            'groups_rights_modules',
            ['group_id' => 1, 'module' => 'test']
        );

        Model::deleteModulePermissions(
            [
                ['group_id' => 1, 'module' => 'test'],
            ]
        );

        $modulePermissions = BackendModel::getContainer()->get('database')->getRecords(
            'SELECT * FROM groups_rights_actions WHERE group_id = ? AND module = ?',
            [1, 'test']
        );

        $this->assertNull($modulePermissions);
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
        BackendModel::getContainer()->get('database')->insert(
            'groups_rights_actions',
            ['group_id' => 1, 'module' => 'test', 'action' => 'test_action', 'level' => 7]
        );

        $exists = Model::existsActionPermission(
            ['group_id' => 1, 'module' => 'test', 'action' => 'test_action', 'level' => 7]
        );

        $this->assertTrue($exists);
    }

    public function testExistsModulePermission(): void
    {
        BackendModel::getContainer()->get('database')->insert(
            'groups_rights_modules',
            ['group_id' => 1, 'module' => 'test']
        );

        $exists = Model::existsModulePermission(['group_id' => 1, 'module' => 'test']);

        $this->assertTrue($exists);
    }

    public function testExistsModulePermissionForUnexistingGroup()
    {
        $exists = Model::existsModulePermission(['group_id' => 10, 'module' => 'test']);

        $this->assertFalse($exists);
    }

    public function testExistsModulePermissionForUnexistingModule()
    {
        $exists = Model::existsModulePermission(['group_id' => 10, 'module' => 'unexisting']);

        $this->assertFalse($exists);
    }

    public function testExistsModulePermissionForNullGroupAndModule()
    {
        $exists = Model::existsModulePermission(['group_id' => null, 'module' => null]);

        $this->assertFalse($exists);
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

    public function testGetUnexistingGroup()
    {
        $group = Model::get(10);

        $this->assertEquals([], $group);
    }

    public function testGetActionPermissions(): void
    {
        $actionPermissions = Model::getActionPermissions(1);

        $this->assertCount(145, $actionPermissions);

        foreach ($actionPermissions as $actionPermission) {
            $this->assertArrayHasKey('module', $actionPermission);
            $this->assertArrayHasKey('action', $actionPermission);
        }

        $actionPermissions = Model::getActionPermissions(1000);

        $this->assertCount(0, $actionPermissions);
    }

    public function testGetActionPermissionsForUnexistingGroup(): void
    {
        $actionPermissions = Model::getActionPermissions(1000);

        $this->assertCount(0, $actionPermissions);
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

        $groups = Model::getGroupsByUser(1000);

        $this->assertCount(0, $groups);
    }

    public function testGetGroupsByUserNonExistingUser(): void
    {
        $groups = Model::getGroupsByUser(1000);

        $this->assertCount(0, $groups);
    }

    public function testIsUserInGroup(): void
    {
        $this->assertTrue(Model::isUserInGroup(1, 1));
        $this->assertFalse(Model::isUserInGroup(1, 2));
    }

    public function testGetModulePermissions(): void
    {
        $modulePermissions = Model::getModulePermissions(1);

        $this->assertCount(17, $modulePermissions);

        $modulePermissions = Model::getModulePermissions(1000);

        $this->assertCount(0, $modulePermissions);
    }

    public function testGetModulePermissionsForNonExistingGroup(): void
    {
        $modulePermissions = Model::getModulePermissions(1000);

        $this->assertCount(0, $modulePermissions);
    }

    public function testGetSetting(): void
    {
        $setting = Model::getSetting(1, 'dashboard_sequence');
        $this->assertNotNull($setting);

        $setting = Model::getSetting(1, 'unexisting_setting');
        $this->assertEquals([], $setting);
    }

    public function testGetSettingForNonExistingSetting(): void
    {
        $setting = Model::getSetting(1, 'unexisting_setting');
        $this->assertEquals([], $setting);
    }

    public function testGetUsers(): void
    {
        $users = Model::getUsers(1);

        $this->assertCount(1, $users);

        $users = Model::getUsers(1000);

        $this->assertCount(0, $users);
    }

    public function testGetUsersForNonExistingGroup(): void
    {
        $users = Model::getUsers(1000);

        $this->assertCount(0, $users);
    }

    public function testInsert(): void
    {
        $groupId = Model::insert(
            [
                'name' => 'test',
                'parameters' => null,
            ],
            [
                'name' => 'setting_test',
                'value' => serialize(null),
            ]
        );

        $this->assertInternalType('int', $groupId);

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
        Model::insertMultipleGroups(1, [1, 2, 3]);

        $groups = Model::getGroupsByUser(1);

        $this->assertCount(3, $groups);
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
