<?php

namespace Backend\Modules\Groups\Domain\Group;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Groups\Domain\Group\GroupRepository")
 * @ORM\Table(name="groups")
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $parameters;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Backend\Modules\Groups\Domain\RightsAction\RightsAction", mappedBy="group")
     */
    private $rightsActions;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Backend\Modules\Groups\Domain\RightsModule\RightsModule", mappedBy="group")
     */
    private $rightsModules;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Groups\Domain\Setting\Setting",
     *     mappedBy="group",
     *     cascade={"ALL"},
     *     orphanRemoval=true
     * )
     */
    private $settings;

    /**
     * @param string $name
     * @param null|string $parameters
     */
    public function __construct(string $name, ?string $parameters)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->rightsActions = new ArrayCollection();
        $this->rightsModules = new ArrayCollection();
        $this->settings = new ArrayCollection();
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

    public function getRightsActions(): Collection
    {
        return $this->rightsActions;
    }

    public function getRightsModules(): Collection
    {
        return $this->rightsModules;
    }

    public function getSettings(): Collection
    {
        return $this->settings;
    }
}
