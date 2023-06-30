<?php

namespace App\Controller;

use App\dto\ConvertersDTO;
use App\Entity\Comentario;
use App\Entity\Publicacion;
use App\Entity\Usuario;
use App\Utilidades\Utils;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use ReallySimpleJWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ComentarioController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    #[Route('/api/comentarios/crear', name: 'app_comentarios_crear', methods: ['POST'])]
    #[OA\Tag(name: 'Comentarios')]
    #[Security(name: "apiKey")]
    public function crearC(Request $request, Utils $utils): JsonResponse
        {
            //Autowireds
            $em = $this->doctrine->getManager();
            $comentarioRepository = $em->getRepository(Comentario::class);
            $usuarioRepository = $em->getRepository(Usuario::class);
            $publicacionRepository = $em->getRepository(Publicacion::class);
            $json = json_decode($request->getContent(), true);

            //Obtenemos los parametros
            $token = $request->headers->get('token');
            $publicacionId = $request->headers->get('publicacion');
            $valido = $utils->esApiKeyValida($token, null);
            if (!$valido) {

                return $this->json(['message' => "El token de sesion ha caducado"], 400);

            } else {

        if (!$json['texto']) {
            return $this->json(['message' => "No ha rellenado los campos requeridos"], 400);
        }

        $texto = $json['texto'];
        $usuario_id = Token::getPayload($token)['user_id'];
        date_default_timezone_set('Europe/Madrid');
        $fecha = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

        $emisor = $usuarioRepository->findOneBy(array('id' => $usuario_id));

        if ($emisor) {

            $publicacion_id = $publicacionRepository->findOneBy(array('id'=> $publicacionId));

            $newComentario = new Comentario();

            if($publicacion_id){
                $newComentario->setTexto($texto);
                $newComentario->setPublicacion($publicacion_id);
                $newComentario->setFechaPublicacion($fecha);
                $newComentario->setUsuario($emisor);

                $comentarioRepository->save($newComentario, true);
            } else{
                return $this->json(['message' => "No existe la publicacion"]);
            }

        } else {
            return $this->json(['message' => "No existe este usuario"]);
        }
    }

    return $this->json(['message' => "Mensaje enviado"]);
    }



    #[Route('/api/comentario/listar/porP', name: 'app_comentario_porP', methods: ['GET'])]
    #[OA\Tag(name: 'Comentario')]
    public function listarPorP(Utils $utils, Request $request,ConvertersDTO $converters): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $publicacionRepository = $em->getRepository(Publicacion::class);
        $comentarioRepository = $em->getRepository(Comentario::class);
        //Obtenemos los parametros
        $publicacionId = $request->headers->get('publicacion');

        $publicacion = $publicacionRepository->findOneBy(array('id' => $publicacionId));

        $listaP = $comentarioRepository->findByPublicacion($publicacion);

            if($listaP){
                return $this->publicacionToJson($listaP, $converters, $utils);
            } else {
                return $this->json(['message' => "No hay mensaje"], 400);
            }
        }




    // METODO EXTRAIDO QUE CONVIERTE LAS LISTAS DE PUBLICACIONES A JSON

    /**
     * @param mixed $listaComentarios
     * @param ConvertersDTO $converters
     * @param Utils $utilidades
     * @return JsonResponse
     */
    public function publicacionToJson(mixed $listaComentarios, ConvertersDTO $converters, Utils $utilidades): JsonResponse
    {
        $listJson = array();

        foreach ($listaComentarios as $comentario) {

            $comentarioDto = $converters->comentarioToDto($comentario);
            $comentarioDto->setFechaPublicacion($comentario->getFechaPublicacion()->format('Y-m-d H:i:s'));

            $json = $utilidades->toJson($comentarioDto, null);
            $listJson[] = json_decode($json, true);
        }

        return new JsonResponse($listJson, 200, [], false);
    }

    #[Route('/api/comentario/delete', name: 'app_comentario_borrar', methods: ["DELETE"])]
    #[OA\Tag(name: 'Comentario')]
    public function eliminarC(Request $request): JsonResponse
    {

        //Autowireds
        $em = $this->doctrine->getManager();
        $comentarioRepository = $em->getRepository(Comentario::class);

        //Obtener Json del body
        $comentarioId = $request->headers->get('eliminarComentarioID');
        $comentario = $comentarioRepository->findOneBy(array('id' => $comentarioId));

        if ($comentario != null) {

            if ($comentarioId == $comentario->getId()) {
                $comentarioRepository->remove($comentario, true);
                return $this->json(['message' => "Elimado comentario"]);

            }

        }

        return $this->json(['message' => "No existe el comentario"], 400);
    }



}