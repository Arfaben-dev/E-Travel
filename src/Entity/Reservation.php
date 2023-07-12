<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255, nullable: false)]
    private ?string $nbreplace = null;

    #[ORM\Column(length: 255)]
    private ?string $prix = null;

    #[ORM\Column(length: 255)]
    private ?string $ref = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Annonce $annoncereservation = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $userreservation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $update_at = null;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: Place::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $places;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $method = null;

    #[ORM\Column]
    private ?bool $isPaid = null;


    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->update_at = new \DateTimeImmutable();
        $this->places = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbreplace(): ?string
    {
        return $this->nbreplace;
    }

    public function setNbreplace(string $nbreplace): self
    {
        $this->nbreplace = $nbreplace;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getAnnoncereservation(): ?Annonce
    {
        return $this->annoncereservation;
    }

    public function setAnnoncereservation(?Annonce $annoncereservation): self
    {
        $this->annoncereservation = $annoncereservation;

        return $this;
    }

    public function getUserreservation(): ?User
    {
        return $this->userreservation;
    }

    public function setUserreservation(?User $userreservation): self
    {
        $this->userreservation = $userreservation;

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
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->setReservation($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getReservation() === $this) {
                $place->setReservation(null);
            }
        }

        return $this;
    }


    public function isreserByUser(User $user): bool
    {
        return $this->userreservation->contains($user);
    }


    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

}
