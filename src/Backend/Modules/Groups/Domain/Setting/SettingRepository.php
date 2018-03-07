<?php

namespace Backend\Modules\Groups\Domain\Setting;

use Doctrine\ORM\EntityRepository;

class SettingRepository extends EntityRepository
{
    public function add(Setting $setting): void
    {
        $this->getEntityManager()->persist($setting);
        $this->getEntityManager()->flush($setting);
    }
}
