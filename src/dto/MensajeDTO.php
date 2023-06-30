<?php

namespace App\dto;

use App\Entity\Usuario;
use DateTime;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Serializer\Annotation\Ignore;

class MensajeDTO
{

    private int $id;

    private string $texto;

    private string $fecha;

    private UsuarioDTO $emisor;

    private UsuarioDTO $receptor;

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
    public function getFecha(): string
    {
        return $this->fecha;
    }

    /**
     * @param string $fecha
     */
    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
    }

    /**
     * @return UsuarioDTO
     */
    public function getEmisor(): UsuarioDTO
    {
        return $this->emisor;
    }

    /**
     * @param UsuarioDTO $emisor
     */
    public function setEmisor(UsuarioDTO $emisor): void
    {
        $this->emisor = $emisor;
    }

    /**
     * @return UsuarioDTO
     */
    public function getReceptor(): UsuarioDTO
    {
        return $this->receptor;
    }

    /**
     * @param UsuarioDTO $receptor
     */
    public function setReceptor(UsuarioDTO $receptor): void
    {
        $this->receptor = $receptor;
    }



}