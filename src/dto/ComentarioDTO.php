<?php

namespace App\dto;

use App\Entity\Comentario;

class ComentarioDTO
{
    private int $id;
    private string $texto;
    private string $fecha_publicacion;

    private UsuarioDTO $usuario;

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
    public function getTexto(): string
    {
        return $this->texto;
    }

    /**
     * @param string $texto
     */
    public function setTexto(string $texto): void
    {
        $this->texto = $texto;
    }

    /**
     * @return string
     */
    public function getFechaPublicacion(): string
    {
        return $this->fecha_publicacion;
    }

    /**
     * @param string $fecha_publicacion
     */
    public function setFechaPublicacion(string $fecha_publicacion): void
    {
        $this->fecha_publicacion = $fecha_publicacion;
    }

    /**
     * @return UsuarioDTO
     */
    public function getUsuario(): UsuarioDTO
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioDTO $usuario
     */
    public function setUsuario(UsuarioDTO $usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * @return PublicacionDTO
     */
    public function getPublicacion(): PublicacionDTO
    {
        return $this->publicacion;
    }

    /**
     * @param PublicacionDTO $publicacion
     */
    public function setPublicacion(PublicacionDTO $publicacion): void
    {
        $this->publicacion = $publicacion;
    }

    private PublicacionDTO $publicacion;

}