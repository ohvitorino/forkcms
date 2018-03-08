<?php

namespace Backend\Modules\Groups\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Domain\Group\Group;
use Backend\Modules\Groups\Domain\Group\GroupRepository;
use Backend\Modules\Groups\Domain\RightsAction\RightsAction;
use Backend\Modules\Groups\Domain\RightsAction\RightsActionRepository;
use Backend\Modules\Groups\Domain\RightsModule\RightsModule;
use Backend\Modules\Groups\Domain\RightsModule\RightsModuleRepository;
use Backend\Modules\Groups\Domain\Setting\Setting;

/**
 * In this file we store all generic functions that we will be using in the groups module.
 */
class Model
{
    const QUERY_BROWSE =
        'SELECT g.id, g.name, COUNT(u.id) AS num_users
         FROM groups AS g
         LEFT OUTER JOIN users_groups AS ug ON g.id = ug.group_id
         LEFT OUTER JOIN users AS u ON u.id = ug.user_id
         GROUP BY g.id';

    const QUERY_ACTIVE_USERS =
        'SELECT u.id, u.email
         FROM users AS u
         INNER JOIN users_groups AS ug ON u.id = ug.user_id
         WHERE ug.group_id = ? AND u.deleted = ?';

    public static function addActionPermissions(array $actionPermissions): void
    {
        /** @var GroupRepository $groupRepository */
        $groupRepository = BackendModel::getContainer()->get('groups.repository.group');
        /** @var RightsActionRepository $rightsActionRepository */
        $rightsActionRepository = BackendModel::getContainer()->get('groups.repository.rights_action');

        foreach ((array) $actionPermissions as $permission) {
            if (!self::existsActionPermission($permission)) {
                /** @var Group $group */
                $group = $groupRepository->find($permission['group_id']);

                if (!$group instanceof Group) {
                    continue;
                }

                $rightsActionRepository->add(
                    new RightsAction($group, $permission['module'], $permission['action'], $permission['level'])
                );
            }
        }
    }

    public static function addModulePermissions(array $modulePermissions): void
    {
        /** @var GroupRepository $groupRepository */
        $groupRepository = BackendModel::getContainer()->get('groups.repository.group');
        /** @var RightsModuleRepository $rightsModuleRepository */
        $rightsModuleRepository = BackendModel::getContainer()->get('groups.repository.rights_module');

        foreach ((array) $modulePermissions as $permission) {
            if (!self::existsModulePermission($permission)) {
                /** @var Group $group */
                $group = $groupRepository->find($permission['group_id']);

                if (!$group instanceof Group) {
                    continue;
                }

                $rightsModuleRepository->add(new RightsModule($group, $permission['module']));
            }
        }
    }

    public static function alreadyExists(string $groupName): bool
    {
        /** @var GroupRepository $groupRepository */
        $groupRepository = BackendModel::get('groups.repository.group');

        return $groupRepository->findOneBy(['name' => $groupName]) instanceof Group;
    }

    public static function delete(int $groupId): void
    {
        /** @var GroupRepository $groupRepository */
        $groupRepository = BackendModel::get('groups.repository.group');
        $group = $groupRepository->findOneBy(['id' => $groupId]);

        if (!$group instanceof Group) {
            return;
        }

        $groupRepository->remove($group);
    }

    public static function deleteActionPermissions(array $actionPermissions): void
    {
        foreach ((array) $actionPermissions as $permission) {
            if (self::existsActionPermission($permission)) {
                /** @var RightsActionRepository $rightsActionRepository */
                $rightsActionRepository = BackendModel::getContainer()->get('groups.repository.rights_action');

                $rightsAction = $rightsActionRepository->findOneBy(
                    [
                        'group' => $permission['group_id'],
                        'module' => $permission['module'],
                        'action' => $permission['action'],
                    ]
                );

                if (!$rightsAction instanceof RightsAction) {
                    return;
                }

                $rightsActionRepository->remove($rightsAction);
            }
        }
    }

    public static function deleteModulePermissions(array $modulePermissions): void
    {
        foreach ((array) $modulePermissions as $permission) {
            if (self::existsModulePermission($permission)) {
                BackendModel::getContainer()->get('database')->delete(
                    'groups_rights_modules',
                    'group_id = ? AND module = ?',
                    [$permission['group_id'], $permission['module']]
                );
            }
        }
    }

    public static function deleteMultipleGroups(int $userId): void
    {
        BackendModel::getContainer()->get('database')->delete('users_groups', 'user_id = ?', [$userId]);
    }

    /**
     * Check if a group already exists
     *
     * @param int $id The id to check upon.
     *
     * @return bool
     */
    public static function exists(int $id): bool
    {
        /** @var GroupRepository $groupRepository */
        $groupRepository = BackendModel::get('groups.repository.group');

        return $groupRepository->find($id) instanceof Group;
    }

    public static function existsActionPermission(array $permission): bool
    {
        /** @var RightsActionRepository $rightActionRepository */
        $rightActionRepository = BackendModel::getContainer()->get('groups.repository.rights_action');

        return $rightActionRepository->findOneBy(
            [
                'module' => $permission['module'],
                'group' => $permission['group_id'],
                'action' => $permission['action'],
            ]
        ) instanceof RightsAction;
    }

    public static function existsModulePermission(array $permission): bool
    {
        /** @var RightsModuleRepository $rightsModuleRepository */
        $rightsModuleRepository = BackendModel::getContainer()->get('groups.repository.rights_module');

        return $rightsModuleRepository->findOneBy(
            [
                'group' => $permission['group_id'],
                'module' => $permission['module'],
            ]
        ) instanceof RightsModule;
    }

    public static function get(int $groupId): array
    {
        /** @var GroupRepository $groupRepository */
        $groupRepository = BackendModel::get('groups.repository.group');

        /** @var Group $group */
        $group = $groupRepository->find($groupId);

        if (!$group instanceof Group) {
            return [];
        }

        return $group->jsonSerialize();
    }

    public static function getActionPermissions(int $groupId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.module, i.action
             FROM groups_rights_actions AS i
             WHERE i.group_id = ?',
            [$groupId]
        );
    }

    public static function getAll(): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS value, i.name AS label FROM groups AS i'
        );
    }

    public static function getGroupsByUser(int $userId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.name
             FROM groups AS i
             INNER JOIN users_groups AS ug ON i.id = ug.group_id
             WHERE ug.user_id = ?',
            [$userId]
        );
    }

    public static function isUserInGroup(int $userId, int $groupId): bool
    {
        $groupsByUser = static::getGroupsByUser($userId);

        foreach ($groupsByUser as $group) {
            if ($group['id'] === $groupId) {
                return true;
            }
        }

        return false;
    }

    public static function getModulePermissions(int $groupId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*
             FROM groups_rights_modules AS i
             WHERE i.group_id = ?',
            [$groupId]
        );
    }

    public static function getSetting(int $groupId, string $settingName): array
    {
        $setting = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.value
             FROM groups_settings AS i
             WHERE i.group_id = ? AND i.name = ?',
            [$groupId, $settingName]
        );

        if (empty($setting)) {
            return [];
        }

        if (isset($setting['value'])) {
            return unserialize($setting['value']);
        }

        return [];
    }

    public static function getUsers(int $groupId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*
             FROM users AS i
             INNER JOIN users_groups AS ug ON i.id = ug.user_id
             WHERE ug.group_id = ? AND i.deleted = ? AND i.active = ?',
            [$groupId, false, true]
        );
    }

    /**
     * Insert a group and a setting
     */
    public static function insert(array $group, array $setting): int
    {
        /** @var GroupRepository $groupRepository */
        $groupRepository = BackendModel::get('groups.repository.group');
        $groupObject = new Group($group['name'], $group['parameters']);
        $groupRepository->add($groupObject);

        $groupObject->getSettings()->add(new Setting($groupObject, $setting['name'], $setting['value']));

        $groupRepository->save($groupObject);

        return $groupObject->getId();
    }

    public static function insertMultipleGroups(int $userId, array $groups): void
    {
        // delete all previous user groups
        self::deleteMultipleGroups($userId);

        // loop through groups

        foreach ($groups as $group) {
            // insert item
            BackendModel::getContainer()->get('database')->insert(
                'users_groups',
                ['user_id' => $userId, 'group_id' => $group]
            );
        }
    }

    /**
     * Update a group
     *
     * @param array $group The group to update.
     * @param array $setting The setting to update.
     */
    public static function update(array $group, array $setting): void
    {
        /** @var GroupRepository $groupRepository */
        $groupRepository = BackendModel::get('groups.repository.group');
        /** @var Group $group */
        $groupObject = $groupRepository->find($group['id']);

        if (!$groupObject instanceof Group) {
            return;
        }

        $groupObject->update($group['name'], $group['parameters']);

        /** @var Setting $settingObject */
        foreach ($groupObject->getSettings() as $settingObject) {
            if ($settingObject->getName() === $setting['name']) {
                $settingObject->update($setting['name'], $setting['value']);

                break;
            }
        }

        $groupRepository->save($groupObject);
    }

    public static function updateSetting(array $setting): void
    {
        BackendModel::getContainer()->get('database')->update(
            'groups_settings',
            ['value' => $setting['value']],
            'group_id = ? AND name = ?',
            [$setting['group_id'], $setting['name']]
        );
    }
}
