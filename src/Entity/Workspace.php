<?php

namespace App\Entity;

use App\Repository\WorkspaceRepository;
use App\Trait\Entity\Codeable;
use App\Trait\Entity\Timestampable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'workspaces')]
#[ORM\Entity(repositoryClass: WorkspaceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Workspace
{
    use Timestampable;
    use Codeable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 125)]
    private ?string $name = null;

    #[ORM\Column(length: 125)]
    private ?string $code = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'workspaces')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Workspace
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): Workspace
    {
        $this->code = $code;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Workspace
    {
        $this->user = $user;

        return $this;
    }

    private function getCodeableName(): string
    {
        return $this->name;
    }
}
