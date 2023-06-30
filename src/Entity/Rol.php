<?php

namespace App\Entity;

use App\Repository\RolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolRepository::class)]
#[ORM\Table(name: 'rol', schema: 'fastspeak')]
class Rol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $nombre = null;

    #[ORM\OneToMany(mappedBy: 'rol', targetEntity: Permiso::class)]
    private Collection $permisos;

    #[ORM\OneToMany(mappedBy: 'rol', targetEntity: RolUsuarioComunidad::class)]
    private Collection $rolUsuarioCEntities;

    public function __construct()
    {
        $this->permisos = new ArrayCollection();
        $this->rolUsuarioCEntities = new ArrayCollection();
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

    /**
     * @return Collection<int, Permiso>
     */
    public function getPermisos(): Collection
    {
        return $this->permisos;
    }

    public function addPermiso(Permiso $permiso): self
    {
        if (!$this->permisos->contains($permiso)) {
            $this->permisos->add($permiso);
            $permiso->setRol($this);
        }

        return $this;
    }

    public function removePermiso(Permiso $permiso): self
    {
        if ($this->permisos->removeElement($permiso)) {
            // set the owning side to null (unless already changed)
            if ($permiso->getRol() === $this) {
                $permiso->setRol(null);
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
            $rolUsuarioCEntity->setRol($this);
        }

        return $this;
    }

    public function removeRolUsuarioCEntity(RolUsuarioComunidad $rolUsuarioCEntity): self
    {
        if ($this->rolUsuarioCEntities->removeElement($rolUsuarioCEntity)) {
            // set the owning side to null (unless already changed)
            if ($rolUsuarioCEntity->getRol() === $this) {
                $rolUsuarioCEntity->setRol(null);
            }
        }

        return $this;
    }
}
