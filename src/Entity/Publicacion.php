<?php

namespace App\Entity;

use App\Repository\PublicacionRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicacionRepository::class)]
#[ORM\Table(name: 'publicacion', schema: 'fastspeak')]
class Publicacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $titulo = null;

    #[ORM\Column(length: 255)]
    private ?string $texto = null;

    #[ORM\Column(length: 2500, nullable: true)]
    private ?string $link = null;

    #[ORM\ManyToOne(inversedBy: 'publicaciones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Archivo $archivo = null;

    #[ORM\ManyToOne(inversedBy: 'publicaciones')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Comunidad $comunidad = null;

    #[ORM\Column(nullable: false)]
    private ?int $upvote = null;

    #[ORM\Column(nullable: false)]
    private ?int $downvote = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $fecha_publicacion = null;

    #[ORM\OneToMany(mappedBy: 'publicacion', targetEntity: Comentario::class)]
    private Collection $comentarios;

    #[ORM\ManyToMany(targetEntity: Tags::class, mappedBy: 'publicacion')]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'publicacion', targetEntity: PublicacionTags::class)]
    private Collection $publicacionTags;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->publicacionTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): self
    {
        $this->texto = $texto;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getArchivo(): ?Archivo
    {
        return $this->archivo;
    }

    public function setArchivo(?Archivo $archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }

    public function getComunidad(): ?Comunidad
    {
        return $this->comunidad;
    }

    public function setComunidad(?Comunidad $comunidad): self
    {
        $this->comunidad = $comunidad;

        return $this;
    }

    public function getUpvote(): ?int
    {
        return $this->upvote;
    }

    public function setUpvote(?int $upvote): self
    {
        $this->upvote = $upvote;

        return $this;
    }

    public function getDownvote(): ?int
    {
        return $this->downvote;
    }

    public function setDownvote(?int $downvote): self
    {
        $this->downvote = $downvote;

        return $this;
    }

    public function getFechaPublicacion(): ?DateTime
    {
        return $this->fecha_publicacion;
    }

    public function setFechaPublicacion(DateTime $fecha_publicacion): self
    {
        $this->fecha_publicacion = $fecha_publicacion;

        return $this;
    }

    /**
     * @return Collection<int, Comentario>
     */
    public function getComentarios(): Collection
    {
        return $this->comentarios;
    }

    public function addComentario(Comentario $comentario): self
    {
        if (!$this->comentarios->contains($comentario)) {
            $this->comentarios->add($comentario);
            $comentario->setPublicacion($this);
        }

        return $this;
    }

    public function removeComentario(Comentario $comentario): self
    {
        if ($this->comentarios->removeElement($comentario)) {
            // set the owning side to null (unless already changed)
            if ($comentario->getPublicacion() === $this) {
                $comentario->setPublicacion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addPublicacion($this);
        }

        return $this;
    }

    public function removeTag(Tags $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removePublicacion($this);
        }

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
            $publicacionTag->setPublicacion($this);
        }

        return $this;
    }

    public function removePublicacionTag(PublicacionTags $publicacionTag): self
    {
        if ($this->publicacionTags->removeElement($publicacionTag)) {
            // set the owning side to null (unless already changed)
            if ($publicacionTag->getPublicacion() === $this) {
                $publicacionTag->setPublicacion(null);
            }
        }

        return $this;
    }


}
