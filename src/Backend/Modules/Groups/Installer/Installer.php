<?php

namespace Backend\Modules\Groups\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Groups\Domain\Group\Group;
use Backend\Modules\Groups\Domain\RightsAction\RightsAction;
use Backend\Modules\Groups\Domain\RightsModule\RightsModule;
use Backend\Modules\Groups\Domain\Setting\Setting;

/**
 * Installer for the groups module
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('Groups');
        $this->configureEntities();
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureBackendWidgets();
    }

    private function configureEntities(): void
    {
        Model::get('fork.entity.create_schema')->forEntityClasses(
            [
                Group::class,
                RightsAction::class,
                RightsModule::class,
                Setting::class,
            ]
        );
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $this->setNavigation(
            $navigationSettingsId,
            'Groups',
            'groups/index',
            [
                'groups/add',
                'groups/edit',
            ],
            5
        );
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
    }

    private function configureBackendWidgets(): void
    {
        $database = $this->getDatabase();

        // build group setting
        $groupSetting = [];
        $groupSetting['group_id'] = 1;
        $groupSetting['name'] = 'dashboard_sequence';
        $groupSetting['value'] = serialize([]);

        // build user setting
        $userSetting = [];
        $userSetting['user_id'] = 1;
        $userSetting['name'] = 'dashboard_sequence';
        $userSetting['value'] = serialize([]);

        // insert settings
        $database->insert('groups_settings', $groupSetting);
        $database->insert('users_settings', $userSetting);

        // insert default dashboard widget
        $this->insertDashboardWidget('Settings', 'Analyse');

        // insert default dashboard widget
        $this->insertDashboardWidget('Users', 'Statistics');
    }
}
