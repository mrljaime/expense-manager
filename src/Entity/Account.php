<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use App\Trait\Entity\Codeable;
use App\Trait\Entity\Timestampable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'accounts')]
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Account
{
    use Timestampable;
    use Codeable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 75)]
    private ?string $name = null;

    #[ORM\Column(length: 125)]
    private ?string $code = null;

    #[ORM\Column(length: 125)]
    private ?string $number = null;

    #[ORM\Column(length: 6)]
    private ?string $currency = null;

    #[ORM\ManyToOne(targetEntity: Workspace::class)]
    private ?Workspace $workspace = null;

    #[ORM\ManyToOne(targetEntity: Institution::class)]
    private ?Institution $institution = null;

    #[ORM\ManyToOne(
        targetEntity: User::class,
        inversedBy: 'accounts'
    )]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Account
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): Account
    {
        $this->code = $code;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): Account
    {
        $this->number = $number;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): Account
    {
        $this->currency = $currency;

        return $this;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(Workspace $workspace): Account
    {
        $this->workspace = $workspace;

        return $this;
    }

    public function getInstitution(): Institution
    {
        return $this->institution;
    }

    public function setInstitution(Institution $institution): Account
    {
        $this->institution = $institution;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Account
    {
        $this->user = $user;

        return $this;
    }

    private function getCodeableName(): string
    {
        return $this->name;
    }
}
