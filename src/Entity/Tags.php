<?php

namespace App\Entity;

use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagsRepository::class)]
#[ORM\Table(name: 'tags', schema: 'fastspeak')]
class Tags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\ManyToMany(targetEntity: Publicacion::class, inversedBy: 'tags')]
    private Collection $publicacion;

    #[ORM\OneToMany(mappedBy: 'tag', targetEntity: PublicacionTags::class)]
    private Collection $publicacionTags;

    public function __construct()
    {
        $this->publicacion = new ArrayCollection();
        $this->publicacionTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection<int, Publicacion>
     */
    public function getPublicacion(): Collection
    {
        return $this->publicacion;
    }

    public function addPublicacion(Publicacion $publicacion): self
    {
        if (!$this->publicacion->contains($publicacion)) {
            $this->publicacion->add($publicacion);
        }

        return $this;
    }

    public function removePublicacion(Publicacion $publicacion): self
    {
        $this->publicacion->removeElement($publicacion);

        return $this;
    }

    /**
     * @return Collection<int, PublicacionTags>
     */
    public function getPublicacionTags(): Collection
    {
        return $this->publicacionTags;
    }

    public function addPublicacionTag(PublicacionTags $publicacionTag): self
    {
        if (!$this->publicacionTags->contains($publicacionTag)) {
            $this->publicacionTags->add($publicacionTag);
            $publicacionTag->setTag($this);
        }

        return $this;
    }

    public function removePublicacionTag(PublicacionTags $publicacionTag): self
    {
        if ($this->publicacionTags->removeElement($publicacionTag)) {
            // set the owning side to null (unless already changed)
            if ($publicacionTag->getTag() === $this) {
                $publicacionTag->setTag(null);
            }
        }

        return $this;
    }
}
