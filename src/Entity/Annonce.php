<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire')]
    private ?string $citystart = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire')]
    private ?string $cityend = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire')]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire')]
    #[Assert\PositiveOrZero(message: 'Le prix ne peut pas être négatif')]
    private ?float $prix = null;

    #[ORM\Column(nullable: true)]
    private ?float $reduction = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire')]
    private ?\DateTimeInterface $datestart = null;


    #[ORM\Column]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire')]
    #[Assert\PositiveOrZero(message: 'Le prix ne peut pas être négatif')]
    private ?int $placedispo = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $update_at = null;

    #[ORM\ManyToOne(inversedBy: 'user_annonce')]
    private ?User $user = null;



    #[ORM\ManyToOne(inversedBy: 'transports')]
    private ?Transport $transport = null;


    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: Image::class, orphanRemoval: true, cascade: ['persist'])]

    private Collection $images;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire')]

    private ?\DateTimeInterface $hourstart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire')]
    private ?\DateTimeInterface $hourend = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'annonces')]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'commentannonce', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'annoncereservation', targetEntity: Reservation::class, orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $placeprise = null;





    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->update_at = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getCityStart(): ?string
    {
        return $this->citystart;
    }

    public function setCityStart(string $citystart): self
    {
        $this->citystart = $citystart;

        return $this;
    }

    public function getCityEnd(): ?string
    {
        return $this->cityend;
    }

    public function setCityEnd(string $cityend): self
    {
        $this->cityend = $cityend;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getReduction(): ?float
    {
        return $this->reduction;
    }

    public function setReduction(float $reduction): self
    {
        $this->reduction = $reduction;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->datestart;
    }

    public function setDateStart(?\DateTimeInterface $datestart): self
    {
        $this->datestart = $datestart;

        return $this;
    }


    public function getPlaceDispo(): ?int
    {
        return $this->placedispo;
    }

    public function setPlaceDispo(int $placedispo): self
    {
        $this->placedispo = $placedispo;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }



    public function getTransport(): ?Transport
    {
        return $this->transport;
    }

    public function setTransport(?Transport $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAnnonce($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAnnonce() === $this) {
                $image->setAnnonce(null);
            }
        }

        return $this;
    }

    public function getHourstart(): ?\DateTimeInterface
    {
        return $this->hourstart;
    }

    public function setHourstart(\DateTimeInterface $hourstart): self
    {
        $this->hourstart = $hourstart;

        return $this;
    }

    public function getHourend(): ?\DateTimeInterface
    {
        return $this->hourend;
    }

    public function setHourend(\DateTimeInterface $hourend): self
    {
        $this->hourend = $hourend;

        return $this;
    }




    public function __toString(): string
    {
        return $this->getId();
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        $this->likes->removeElement($like);

        return $this;
    }

    public function isLikeByUser(User $user): bool
    {
        return $this->likes->contains($user);
    }


     /**
      * @return Collection<int, Commentaire>
      */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setCommentannonce($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getCommentannonce() === $this) {
                $commentaire->setCommentannonce(null);
            }
        }

        return $this;
    }

     /**
      * @return Collection<int, Reservation>
      */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setAnnoncereservation($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getAnnoncereservation() === $this) {
                $reservation->setAnnoncereservation(null);
            }
        }

        return $this;
    }

    public function getPlaceprise(): ?string
    {
        return $this->placeprise;
    }

    public function setPlaceprise(?string $placeprise): self
    {
        $this->placeprise = $placeprise;

        return $this;
    }
}
