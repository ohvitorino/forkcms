<?php

namespace Backend\Modules\Groups\Domain\Setting;

use Backend\Modules\Groups\Domain\Group\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Groups\Domain\Setting\SettingRepository")
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
     * @var array|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    public function __construct(Group $group, string $name, ?string $value)
    {
        $this->group = $group;
        $this->name = $name;
        $this->value = $value;
    }

    public function update(string $name, ?string $value): void
    {
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

    public function getValue(): ?string
    {
        return $this->value;
    }
}
