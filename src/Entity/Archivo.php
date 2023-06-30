<?php

namespace App\Entity;

use App\Repository\ArchivoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArchivoRepository::class)]
#[ORM\Table(name: 'archivo', schema: 'fastspeak')]
class Archivo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $link_drive = null;

    #[ORM\Column(nullable: true)]
    private ?int $codigo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLinkDrive(): ?string
    {
        return $this->link_drive;
    }

    public function setLinkDrive(?string $link_drive): self
    {
        $this->link_drive = $link_drive;

        return $this;
    }

    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    public function setCodigo(?int $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }
}
