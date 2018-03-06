<?php

namespace Backend\Modules\Groups\Tests\Engine;

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
        $this->markTestSkipped('Not implemented yet');
    }

    public function testAddModulePermisions(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testAlreadyExists(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testDelete(): void
    {
        $this->markTestSkipped('Not implemented yet');
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
        $this->markTestSkipped('Not implemented yet');
    }

    public function testExists(): void
    {
        $this->markTestSkipped('Not implemented yet');
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
        $this->markTestSkipped('Not implemented yet');
    }

    public function testGetActionPermissions(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testGetAll(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testGetGroupsByUser(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testIsUserInGroup(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testGetModulePermissions(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testGetSetting(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testGetUsers(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testInsert(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testInsertMultipleGroups(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testInsertSetting(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testUpdate(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function testUpdateSetting(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }
}
