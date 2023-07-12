<?php

namespace App\Entity;

use App\Repository\TransportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransportRepository::class)]
class Transport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $update_at = null;

    #[ORM\OneToMany(mappedBy: 'transport', targetEntity: Annonce::class)]
    private Collection $transports;

    public function __construct()
    {
        $this->transports = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->update_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->update_at;
    }

    public function setUpdateAt(\DateTimeImmutable $update_at): self
    {
        $this->update_at = $update_at;

        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getTransports(): Collection
    {
        return $this->transports;
    }

    public function addTransport(Annonce $transport): self
    {
        if (!$this->transports->contains($transport)) {
            $this->transports->add($transport);
            $transport->setTransport($this);
        }

        return $this;
    }

    public function removeTransport(Annonce $transport): self
    {
        if ($this->transports->removeElement($transport)) {
            // set the owning side to null (unless already changed)
            if ($transport->getTransport() === $this) {
                $transport->setTransport(null);
            }
        }

        return $this;
    }
         public function __toString(): string
         {
              return  $this->getName();
         }
}
