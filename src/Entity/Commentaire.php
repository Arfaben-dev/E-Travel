<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $note = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $update_at = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Annonce $commentannonce = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?User $commentuser = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;


    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->update_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

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

    public function getCommentannonce(): ?Annonce
    {
        return $this->commentannonce;
    }

    public function setCommentannonce(?Annonce $commentannonce): self
    {
        $this->commentannonce = $commentannonce;

        return $this;
    }

    public function getCommentuser(): ?User
    {
        return $this->commentuser;
    }

    public function setCommentuser(?User $commentuser): self
    {
        $this->commentuser = $commentuser;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
