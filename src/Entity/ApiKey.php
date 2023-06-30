<?php

namespace App\Entity;

use App\Repository\ApiKeyRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiKeyRepository::class)]
#[ORM\Table(name: 'apiKey', schema: 'fastspeak')]
class ApiKey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'bigint', length: 50)]
    private ?int $id = null;

    #[ORM\Column(name: 'token' ,length: 500)]
    private ?string $token = null;

    #[ORM\Column(name: 'fecha_expiracion')]
    private ?DateTime $fechaExpiracion = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getFechaExpiracion(): ?DateTime
    {
        return $this->fechaExpiracion;
    }

    public function setFechaExpiracion(DateTime $fechaExpiracion): self
    {
        $this->fechaExpiracion = $fechaExpiracion;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

}
