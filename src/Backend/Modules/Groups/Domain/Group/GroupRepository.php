<?php

namespace Backend\Modules\Groups\Domain\Group;

use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    public function add(Group $group): void
    {
        $this->getEntityManager()->persist($group);
        $this->getEntityManager()->flush($group);
    }
}
