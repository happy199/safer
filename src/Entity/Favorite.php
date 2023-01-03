<?php

namespace App\Entity;
use App\Entity\Trait\CreatedAtTrait;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{
    use CreatedAtTrait;


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "favorites")]
    #[ORM\JoinColumn(nullable:false, onDelete: "CASCADE")]
    private $user;

    #[ORM\ManyToOne(targetEntity: Property::class, inversedBy: "favorites")]
    #[ORM\JoinColumn(nullable:false, onDelete: "CASCADE")]
    private $property;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

        return $this; 
    }
}

