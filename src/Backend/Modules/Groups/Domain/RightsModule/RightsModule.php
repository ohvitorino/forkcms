<?php

namespace Backend\Modules\Groups\Domain\RightsModule;

use Backend\Modules\Groups\Domain\Group\Group;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Groups\Domain\RightsModule\RightsModuleRepository")
 * @ORM\Table(name="groups_rights_modules")
 */
class RightsModule implements JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Group
     *
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Groups\Domain\Group\Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $group;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $module;

    /**
     * @param Group $group
     * @param string $module
     */
    public function __construct(Group $group, string $module)
    {
        $this->group = $group;
        $this->module = $module;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroup()->getId(),
            'module' => $this->getModule(),
        ];
    }
}
