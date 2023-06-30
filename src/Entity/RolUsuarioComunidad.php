<?php

namespace App\Entity;

use App\Repository\RolUsuarioCRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolUsuarioCRepository::class)]
#[ORM\Table(name: 'ruc', schema: 'fastspeak')]
class RolUsuarioComunidad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'rolUsuarioCEntities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rol $rol = null;

    #[ORM\ManyToOne(inversedBy: 'rolUsuarioCEntities')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Comunidad $comunidad = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRol(): ?Rol
    {
        return $this->rol;
    }

    public function setRol(?Rol $rol): self
    {
        $this->rol = $rol;

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
}
