<?php

namespace App\dto;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PublicacionDTO
{

    private int $id;
    private int $usuario_id;
    private int $archivo_id;
    private int $comunidad_id;
    private string $titulo;
    private string $texto;
    private string $link;
    private int $upvote;
    private int $downvote;
    private string $fecha_publicacion;

    public function __construct()
    {
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
     * @return int
     */
    public function getUsuarioId(): int
    {
        return $this->usuario_id;
    }

    /**
     * @param int $usuario_id
     */
    public function setUsuarioId(int $usuario_id): void
    {
        $this->usuario_id = $usuario_id;
    }

    /**
     * @return int
     */
    public function getArchivoId(): int
    {
        return $this->archivo_id;
    }

    /**
     * @param int $archivo_id
     */
    public function setArchivoId(int $archivo_id): void
    {
        $this->archivo_id = $archivo_id;
    }

    /**
     * @return int
     */
    public function getComunidadId(): int
    {
        return $this->comunidad_id;
    }

    /**
     * @param int $comunidad_id
     */
    public function setComunidadId(int $comunidad_id): void
    {
        $this->comunidad_id = $comunidad_id;
    }

    /**
     * @return string
     */
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    /**
     * @param string $titulo
     */
    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
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
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return int
     */
    public function getUpvote(): int
    {
        return $this->upvote;
    }

    /**
     * @param int $upvote
     */
    public function setUpvote(int $upvote): void
    {
        $this->upvote = $upvote;
    }

    /**
     * @return int
     */
    public function getDownvote(): int
    {
        return $this->downvote;
    }

    /**
     * @param int $downvote
     */
    public function setDownvote(int $downvote): void
    {
        $this->downvote = $downvote;
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




}