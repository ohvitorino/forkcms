<?php

namespace Backend\Modules\Groups\Domain\Setting;

use Backend\Modules\Groups\Domain\Group\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="groups_settings")
 */
class Setting
{
    /**
     * @var Group
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Groups\Domain\Group\Group", inversedBy="settings")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $group;

    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $value;

    /**
     * @param Group $group
     * @param string $name
     * @param array $value
     */
    public function __construct(Group $group, string $name, array $value)
    {
        $this->group = $group;
        $this->name = $name;
        $this->value = $value;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): array
    {
        return $this->value;
    }
}
