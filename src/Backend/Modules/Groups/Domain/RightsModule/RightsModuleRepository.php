<?php

namespace Backend\Modules\Groups\Domain\RightsModule;

use Doctrine\ORM\EntityRepository;

class RightsModuleRepository extends EntityRepository
{
    public function add(RightsModule $rightsModule): void
    {
        $this->getEntityManager()->persist($rightsModule);
        $this->getEntityManager()->flush($rightsModule);
    }

    public function remove(RightsModule $rightsModule): void
    {
        $this->getEntityManager()->remove($rightsModule);
        $this->getEntityManager()->flush($rightsModule);
    }
}
