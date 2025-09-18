<?php

namespace App\Trait\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \App\Trait\Entity\Timestampable.
 *
 * Sets timestamps in entities
 */
trait Timestampable
{
    #[ORM\Column(type: 'datetimetz_immutable')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetimetz_immutable')]
    private ?\DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function preCreatedAt(): void
    {
        if (!is_null($this->createdAt)) {
            return;
        }

        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    #[ORM\PreUpdate]
    public function preUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }
}
