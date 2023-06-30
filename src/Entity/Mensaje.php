<?php

namespace App\Entity;

use App\Repository\MensajeRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MensajeRepository::class)]
#[ORM\Table(name: 'mensaje', schema: 'fastspeak')]
class Mensaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'bigint', length: 50)]
    private ?int $id = null;

    #[ORM\Column(name: 'texto', length: 255)]
    private ?string $texto = null;

    #[ORM\Column(name: 'fecha', type: Types::DATE_MUTABLE)]
    private ?DateTime $fecha = null;

    #[ORM\ManyToOne(inversedBy: 'mensajes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $emisor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $receptor = null;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getFecha(): DateTime
    {
        return $this->fecha;
    }

    public function setFecha(DateTime $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getEmisor(): ?Usuario
    {
        return $this->emisor;
    }

    public function setEmisor(?Usuario $emisor): self
    {
        $this->emisor = $emisor;

        return $this;
    }

    public function getReceptor(): ?Usuario
    {
        return $this->receptor;
    }

    public function setReceptor(?Usuario $receptor): self
    {
        $this->receptor = $receptor;

        return $this;
    }


}
