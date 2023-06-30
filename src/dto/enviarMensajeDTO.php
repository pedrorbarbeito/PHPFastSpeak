<?php

namespace App\dto;

class enviarMensajeDTO
{

    private string $texto;

    private UsuarioDTO $receptor;

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