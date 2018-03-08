<?php

namespace Backend\Modules\Groups\Domain\RightsAction;

use Doctrine\ORM\EntityRepository;

class RightsActionRepository extends EntityRepository
{
    public function add(RightsAction $rightsAction): void
    {
        $this->getEntityManager()->persist($rightsAction);
        $this->getEntityManager()->flush($rightsAction);
    }
}
