<?php

namespace App\Entity;

use App\Enum\Entity\InstitutionType;
use App\Repository\InstitutionRepository;
use App\Trait\Entity\Codeable;
use App\Trait\Entity\Timestampable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'institutions')]
#[ORM\Entity(repositoryClass: InstitutionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Institution
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

    #[ORM\Column(type: 'string', length: 75, enumType: InstitutionType::class)]
    private ?InstitutionType $type = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Institution
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): Institution
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): ?InstitutionType
    {
        return $this->type;
    }

    public function setType(InstitutionType $type): Institution
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Institution
    {
        $this->user = $user;

        return $this;
    }

    private function getCodeableName(): string
    {
        return $this->name;
    }
}
