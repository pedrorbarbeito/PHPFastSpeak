<?php

namespace App\dto;

use App\Entity\Comentario;
use App\Entity\Comunidad;
use App\Entity\Mensaje;
use App\Entity\Publicacion;
use App\Entity\PublicacionTags;
use App\Entity\Rol;
use App\Entity\RolUsuarioComunidad;
use App\Entity\Tags;
use App\Entity\Usuario;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

class ConvertersDTO
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    /**
     * @param Rol $rol
     * @return RolDTO
     */
    public function rolToDto(Rol $rol): RolDTO
    {
        $rolDTO = new RolDTO();
        $rolDTO->setId($rol->getId());
        $rolDTO->setNombre($rol->getNombre());
        return $rolDTO;
    }


    /**
     * @param Usuario $usuario
     * @return UsuarioDTO
     */
    public function usuarioToDto(Usuario $usuario): UsuarioDTO
    {

        //Autowireds
        $em = $this-> doctrine->getManager();

        $usuarioDto = new UsuarioDTO();
        $usuarioDto->setId($usuario->getId());
        $usuarioDto->setUsername($usuario->getUsername());
        $usuarioDto->setPassword($usuario->getPassword());
        $usuarioDto->setEmail($usuario->getEmail());

        if ($usuario->getDescripcion() == null)
        {
            $usuarioDto->setDescripcion("");
        } else {
            $usuarioDto->setDescripcion($usuario->getDescripcion());
        }

        if ($usuario->getFoto() == null){
            $usuarioDto->setFoto("https://img.myloview.com/stickers/default-avatar-profile-icon-vector-social-media-user-700-202768327.jpg");
        } else {
            $usuarioDto->setFoto($usuario->getFoto());
        }

        $roles = new ArrayCollection();
        foreach($usuario->getRoles() as $rucs)
        {
            $roles[] = $rucs->getRol()->getNombre();
        }

        $usuarioDto->setRoles($roles);

        return $usuarioDto;

    }

    /**
     * @param Mensaje $mensaje
     * @return MensajeDTO
     */
  /* public function mensajeToDto(Mensaje $mensaje): MensajeDTO
    {

        //Autowireds
        $em = $this-> doctrine->getManager();
        $rucRepository = $em->getRepository(RolUsuarioComunidad::class);

        $mensajeDTO = new MensajeDTO();
        $mensajeDTO->setId($mensaje->getId());
        $mensajeDTO->setUsuarioId($mensaje->getUsuarioId()->getId());
        $mensajeDTO->setTexto($mensaje->getTexto());
        $mensajeDTO->setFecha($mensaje->getFecha());

        return $mensajeDTO;

    } */

    /**
     * @param Publicacion $publicacion
     * @return PublicacionDTO
     */
    public function publicacionToDto(Publicacion $publicacion): PublicacionDTO
    {

        $publicacionDto = new PublicacionDTO();
        $publicacionDto->setId($publicacion->getId());
        $publicacionDto->setTitulo($publicacion->getTitulo());
        $publicacionDto->setTexto($publicacion->getTexto());
        $publicacionDto->setUpvote($publicacion->getUpvote());
        $publicacionDto->setDownvote($publicacion->getDownvote());
        if ($publicacion->getLink() != null){
            $publicacionDto->setLink($publicacion->getLink());
        }
        $publicacionDto->setUsuarioId($publicacion->getUsuario()->getId());
        if ($publicacion->getComunidad() != null){
            $publicacionDto->setComunidadId($publicacion->getComunidad()->getId());
        }

        return $publicacionDto;
    }

    /**
     * @param Comunidad $comunidad
     * @return ComunidadDTO
     */

    public function comunidadToDto(Comunidad $comunidad): ComunidadDTO
    {
        $comunidadDTO = new ComunidadDTO();
        $comunidadDTO->setId($comunidad->getId());
        $comunidadDTO->setNombre($comunidad->getNombre());
        $comunidadDTO->setTipo($comunidad->getTipo());
        if ($comunidad->getImagen()){
            $comunidadDTO->setImagen($comunidad->getImagen());
        } else {
            $comunidadDTO->setImagen("https://img.myloview.com/stickers/default-avatar-profile-icon-vector-social-media-user-700-202768327.jpg");
        }
        if ($comunidad->getBanner()){
            $comunidadDTO->setBanner($comunidad->getBanner());
        }

        return $comunidadDTO;
    }

    /**
     * @param Mensaje $mensaje
     * @return MensajeDTO
     */

    public function mensajetoDTO(Mensaje $mensaje): MensajeDTO
    {
        $mensajeDTO = new MensajeDTO();
        $mensajeDTO->setId($mensaje->getId());
        $mensajeDTO->setEmisor($this->UsuarioToDTO($mensaje->getEmisor()));
        $mensajeDTO->setReceptor($this->UsuarioToDTO($mensaje->getReceptor()));
        $mensajeDTO->setTexto($mensaje->getTexto());
        $mensajeDTO->setFecha($mensaje->getFecha()->format("d/m/y H:i"));

        return $mensajeDTO;
    }

    /**
     * @param Comentario $comentario
     * @return ComentarioDTO
     */
    public function comentarioToDto(mixed $comentario): ComentarioDTO
    {
        $comentarioDTO = new ComentarioDTO();
        $comentarioDTO->setId($comentario->getId());
        $comentarioDTO->setUsuario($this->UsuarioToDTO($comentario->getUsuario()));
        $comentarioDTO->setPublicacion($this->publicacionToDto($comentario->getPublicacion()));
        $comentarioDTO->setTexto($comentario->getTexto());
        $comentarioDTO->setFechaPublicacion($comentario->getFechaPublicacion()->format("d/m/y H:i"));

        return $comentarioDTO;
    }

    /**
     * @param Tags $tags
     * @return TagsDTO
     */
    public function tagToDto(mixed $tags): TagsDTO
    {
        $tagsDTO = new TagsDTO();
        $tagsDTO->setId($tags->getId());
        $tagsDTO->setNombre($tags->getNombre());
        $tagsDTO->setDescripcion($tags->getDescripcion());

        return $tagsDTO;
    }

    public function publiTagToDto(PublicacionTags $tags): TagsDTO
    {
        $tagsDTO = new TagsDTO();
        $tagsDTO->setId($tags->getPublicacion()->getId());
        $tagsDTO->setNombre($tags->getTag()->getNombre());
        $tagsDTO->setDescripcion($tags->getTag()->getDescripcion());

        return $tagsDTO;
    }


}