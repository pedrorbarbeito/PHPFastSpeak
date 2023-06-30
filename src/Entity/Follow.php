<?php

namespace App\Entity;

use App\Repository\FollowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FollowRepository::class)]
#[ORM\Table(name: 'follow', schema: 'fastspeak')]
class Follow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'bigint', length: 50)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $emisor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Usuario $receptor = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    private ?Comunidad $comunidad = null;

    public function getId(): ?int
    {
        return $this->id;
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
