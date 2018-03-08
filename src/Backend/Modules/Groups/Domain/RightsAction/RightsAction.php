<?php

namespace Backend\Modules\Groups\Domain\RightsAction;

use Backend\Modules\Groups\Domain\Group\Group;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Groups\Domain\RightsAction\RightsActionRepository")
 * @ORM\Table(name="groups_rights_actions")
 */
class RightsAction implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Groups\Domain\Group\Group", inversedBy="rightsActions")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $group;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $action;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $level;

    public function __construct(Group $group, string $module, string $action, int $level)
    {
        $this->group = $group;
        $this->module = $module;
        $this->action = $action;
        $this->level = $level;
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

    public function getAction(): string
    {
        return $this->action;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroup()->getId(),
            'module' => $this->getModule(),
            'action' => $this->getAction(),
            'level' => $this->getLevel(),
        ];
    }
}
