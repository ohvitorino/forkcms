<?php

namespace Backend\Modules\Groups\Domain\Group;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Groups\Domain\Group\GroupRepository")
 * @ORM\Table(name="group")
 */
class Group
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
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $parameters;

    /**
     * @param string $name
     * @param null|string $parameters
     */
    public function __construct(string $name, ?string $parameters)
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameters(): ?string
    {
        return $this->parameters;
    }
}
