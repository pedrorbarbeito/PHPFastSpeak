<?php

namespace App\Entity;

use App\Repository\PermisoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermisoRepository::class)]
#[ORM\Table(name: 'permiso', schema: 'fastspeak')]
class Permiso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ruta = null;

    #[ORM\ManyToOne(inversedBy: 'permisos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rol $rol = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRuta(): ?string
    {
        return $this->ruta;
    }

    public function setRuta(string $ruta): self
    {
        $this->ruta = $ruta;

        return $this;
    }

    public function getRol(): ?Rol
    {
        return $this->rol;
    }

    public function setRol(?Rol $rol): self
    {
        $this->rol = $rol;

        return $this;
    }
}
