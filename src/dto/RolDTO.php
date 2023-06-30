<?php

namespace App\dto;

use App\Entity\Rol;

class RolDTO
{

    private int $id;

    private string $nombre;

    public function __construct()
    {
    }


    /**
     * @param Rol $rol
     */
    public function rolToDto(Rol $rol)
    {
        $this->id=$rol->getId();
        $this->nombre=$rol->getNombre();
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




}