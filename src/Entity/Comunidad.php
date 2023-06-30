<?php

namespace App\Entity;

use App\Repository\ComunidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComunidadRepository::class)]
#[ORM\Table(name: 'comunidad', schema: 'fastspeak')]
class Comunidad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $tipo = null;

    #[ORM\OneToMany(mappedBy: 'comunidad', targetEntity: Publicacion::class)]
    private Collection $publicaciones;

    #[ORM\OneToMany(mappedBy: 'comunidad', targetEntity: RolUsuarioComunidad::class)]
    private Collection $rolUsuarioCEntities;

    #[ORM\ManyToOne(inversedBy: 'comunidades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\OneToMany(mappedBy: 'comunidad', targetEntity: Follow::class)]
    private Collection $follows;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $banner = null;

    public function __construct()
    {
        $this->publicaciones = new ArrayCollection();
        $this->rolUsuarioCEntities = new ArrayCollection();
        $this->follows = new ArrayCollection();
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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return Collection<int, Publicacion>
     */
    public function getPublicaciones(): Collection
    {
        return $this->publicaciones;
    }

    public function addPublicacione(Publicacion $publicacione): self
    {
        if (!$this->publicaciones->contains($publicacione)) {
            $this->publicaciones->add($publicacione);
            $publicacione->setComunidad($this);
        }

        return $this;
    }

    public function removePublicacione(Publicacion $publicacione): self
    {
        if ($this->publicaciones->removeElement($publicacione)) {
            // set the owning side to null (unless already changed)
            if ($publicacione->getComunidad() === $this) {
                $publicacione->setComunidad(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RolUsuarioComunidad>
     */
    public function getRolUsuarioCEntities(): Collection
    {
        return $this->rolUsuarioCEntities;
    }

    public function addRolUsuarioCEntity(RolUsuarioComunidad $rolUsuarioCEntity): self
    {
        if (!$this->rolUsuarioCEntities->contains($rolUsuarioCEntity)) {
            $this->rolUsuarioCEntities->add($rolUsuarioCEntity);
            $rolUsuarioCEntity->setComunidad($this);
        }

        return $this;
    }

    public function removeRolUsuarioCEntity(RolUsuarioComunidad $rolUsuarioCEntity): self
    {
        if ($this->rolUsuarioCEntities->removeElement($rolUsuarioCEntity)) {
            // set the owning side to null (unless already changed)
            if ($rolUsuarioCEntity->getComunidad() === $this) {
                $rolUsuarioCEntity->setComunidad(null);
            }
        }

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

    /**
     * @return Collection<int, Follow>
     */
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    public function addFollow(Follow $follow): self
    {
        if (!$this->follows->contains($follow)) {
            $this->follows->add($follow);
            $follow->setComunidad($this);
        }

        return $this;
    }

    public function removeFollow(Follow $follow): self
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getComunidad() === $this) {
                $follow->setComunidad(null);
            }
        }

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

}
