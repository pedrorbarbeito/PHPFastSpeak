<?php

namespace App\Entity;

use App\Repository\PublicacionTagsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicacionTagsRepository::class)]
#[ORM\Table(name: 'publicacionTags', schema: 'fastspeak')]
class PublicacionTags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'publicacionTags')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publicacion $publicacion = null;

    #[ORM\ManyToOne(inversedBy: 'publicacionTags')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tags $tag = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicacion(): ?Publicacion
    {
        return $this->publicacion;
    }

    public function setPublicacion(?Publicacion $publicacion): self
    {
        $this->publicacion = $publicacion;

        return $this;
    }

    public function getTag(): ?Tags
    {
        return $this->tag;
    }

    public function setTag(?Tags $tag): self
    {
        $this->tag = $tag;

        return $this;
    }


}
