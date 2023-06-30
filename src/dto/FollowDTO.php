<?php

namespace App\dto;

use App\Entity\Usuario;
use Doctrine\Common\Collections\ArrayCollection;

class FollowDTO
{
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }
    private Usuario $emisor;

    private Usuario $receptor;

    /**
     * @return Usuario
     */
    public function getEmisor(): Usuario
    {
        return $this->emisor;
    }

    /**
     * @param Usuario $emisor
     */
    public function setEmisor(Usuario $emisor): void
    {
        $this->emisor = $emisor;
    }

    /**
     * @return Usuario
     */
    public function getReceptor(): Usuario
    {
        return $this->receptor;
    }

    /**
     * @param Usuario $receptor
     */
    public function setReceptor(Usuario $receptor): void
    {
        $this->receptor = $receptor;
    }



}