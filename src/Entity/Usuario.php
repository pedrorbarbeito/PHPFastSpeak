<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\Table(name: 'usuario', schema: 'fastspeak')]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'bigint', length: 50)]
    private ?int $id = null;

    #[ORM\Column(name: 'username', type: 'string', length: 50)]
    private ?string $username = null;

    #[ORM\Column(name: 'password', type: 'string', length: 500)]
    private ?string $password = null;

    #[ORM\Column(name: 'email', type: 'string',length: 50)]
    private ?string $email = null;

    #[ORM\Column(name: 'created_on', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_on = null;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Publicacion::class)]
    private Collection $publicaciones;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: RolUsuarioComunidad::class)]
    private Collection $roles;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Mensaje::class)]
    private Collection $mensajes;

    #[ORM\OneToMany(mappedBy: 'receptor', targetEntity: Mensaje::class)]
    private Collection $mensajesReceptor;

    #[ORM\OneToMany(mappedBy: 'emisor', targetEntity: Follow::class)]
    private Collection $follows;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Comunidad::class)]
    private Collection $comunidades;



    public function __construct()
    {
        $this->publicaciones = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->mensajes = new ArrayCollection();
        $this->mensajesReceptor = new ArrayCollection();
        $this->follows = new ArrayCollection();
        $this->comunidades = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->created_on;
    }

    public function setCreatedOn(\DateTimeInterface $created_on): self
    {
        $this->created_on = $created_on;

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
            $publicacione->setUsuario($this);
        }

        return $this;
    }

    public function removePublicacione(Publicacion $publicacione): self
    {
        if ($this->publicaciones->removeElement($publicacione)) {
            // set the owning side to null (unless already changed)
            if ($publicacione->getUsuario() === $this) {
                $publicacione->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RolUsuarioComunidad>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(RolUsuarioComunidad $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->setUsuario($this);
        }

        return $this;
    }

    public function removeRole(RolUsuarioComunidad $role): self
    {
        if ($this->roles->removeElement($role)) {
            // set the owning side to null (unless already changed)
            if ($role->getUsuario() === $this) {
                $role->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mensaje>
     */
    public function getMensajes(): Collection
    {
        return $this->mensajes;
    }

    public function addMensaje(Mensaje $mensaje): self
    {
        if (!$this->mensajes->contains($mensaje)) {
            $this->mensajes->add($mensaje);
            $mensaje->setUsuarioId($this);
        }

        return $this;
    }

    public function removeMensaje(Mensaje $mensaje): self
    {
        if ($this->mensajes->removeElement($mensaje)) {
            // set the owning side to null (unless already changed)
            if ($mensaje->getUsuarioId() === $this) {
                $mensaje->setUsuarioId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mensaje>
     */
    public function getMensajesReceptor(): Collection
    {
        return $this->mensajesReceptor;
    }

    public function addMensajesReceptor(Mensaje $mensajesReceptor): self
    {
        if (!$this->mensajesReceptor->contains($mensajesReceptor)) {
            $this->mensajesReceptor->add($mensajesReceptor);
            $mensajesReceptor->setReceptor($this);
        }

        return $this;
    }

    public function removeMensajesReceptor(Mensaje $mensajesReceptor): self
    {
        if ($this->mensajesReceptor->removeElement($mensajesReceptor)) {
            // set the owning side to null (unless already changed)
            if ($mensajesReceptor->getReceptor() === $this) {
                $mensajesReceptor->setReceptor(null);
            }
        }

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
            $follow->setEmisor($this);
        }

        return $this;
    }

    public function removeFollow(Follow $follow): self
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getEmisor() === $this) {
                $follow->setEmisor(null);
            }
        }

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

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * @return Collection<int, Comunidad>
     */
    public function getComunidades(): Collection
    {
        return $this->comunidades;
    }

    public function addComunidade(Comunidad $comunidade): self
    {
        if (!$this->comunidades->contains($comunidade)) {
            $this->comunidades->add($comunidade);
            $comunidade->setUsuario($this);
        }

        return $this;
    }

    public function removeComunidade(Comunidad $comunidade): self
    {
        if ($this->comunidades->removeElement($comunidade)) {
            // set the owning side to null (unless already changed)
            if ($comunidade->getUsuario() === $this) {
                $comunidade->setUsuario(null);
            }
        }

        return $this;
    }
}