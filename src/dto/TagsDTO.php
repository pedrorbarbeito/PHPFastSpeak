<?php

namespace App\dto;

use Doctrine\Common\Collections\Collection;

class TagsDTO
{
    private int $id;
    private string $nombre;
    private string $descripcion;
    private Collection $publicacion;
    private Collection $publicacionTags;

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
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return Collection
     */
    public function getPublicacion(): Collection
    {
        return $this->publicacion;
    }

    /**
     * @param Collection $publicacion
     */
    public function setPublicacion(Collection $publicacion): void
    {
        $this->publicacion = $publicacion;
    }

    /**
     * @return Collection
     */
    public function getPublicacionTags(): Collection
    {
        return $this->publicacionTags;
    }

    /**
     * @param Collection $publicacionTags
     */
    public function setPublicacionTags(Collection $publicacionTags): void
    {
        $this->publicacionTags = $publicacionTags;
    }

}