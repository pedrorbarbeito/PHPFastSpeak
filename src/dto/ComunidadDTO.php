<?php

namespace App\dto;

use Doctrine\Common\Collections\Collection;

class ComunidadDTO
{
    private int $id;
    private string $nombre;
    private string $tipo;

    private Collection $publicaciones;
    private Collection $rolUsuarioCEntities;

    private Collection $usuario;

    private string $imagen;

    private string $banner;

    /**
     * @return Collection
     */
    public function getUsuario(): Collection
    {
        return $this->usuario;
    }

    /**
     * @param Collection $usuario
     */
    public function setUsuario(Collection $usuario): void
    {
        $this->usuario = $usuario;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getTipo(): string
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     */
    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @return Collection
     */
    public function getPublicaciones(): Collection
    {
        return $this->publicaciones;
    }

    /**
     * @param Collection $publicaciones
     */
    public function setPublicaciones(Collection $publicaciones): void
    {
        $this->publicaciones = $publicaciones;
    }

    /**
     * @return Collection
     */
    public function getRolUsuarioCEntities(): Collection
    {
        return $this->rolUsuarioCEntities;
    }

    /**
     * @param Collection $rolUsuarioCEntities
     */
    public function setRolUsuarioCEntities(Collection $rolUsuarioCEntities): void
    {
        $this->rolUsuarioCEntities = $rolUsuarioCEntities;
    }

    /**
     * @return string
     */
    public function getImagen(): string
    {
        return $this->imagen;
    }

    /**
     * @param string $imagen
     */
    public function setImagen(string $imagen): void
    {
        $this->imagen = $imagen;
    }

    /**
     * @return string
     */
    public function getBanner(): string
    {
        return $this->banner;
    }

    /**
     * @param string $banner
     */
    public function setBanner(string $banner): void
    {
        $this->banner = $banner;
    }



}