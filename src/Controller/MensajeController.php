<?php

namespace App\Controller;

use App\dto\ConvertersDTO;
use App\Entity\ApiKey;
use App\Entity\Mensaje;
use App\Entity\Usuario;
use App\Repository\MensajeRepository;
use App\Utilidades\Utils;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use ReallySimpleJWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;


class MensajeController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->doctrine = $managerRegistry;
    }


    //Crear mensaje
    #[Route('/api/mensaje/crear', name: 'app_mensaje_crear', methods: ['POST'])]
    #[OA\Tag(name: 'Mensaje')]
    public function enviar(Request $request, Utils $utils): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $mensajeRepository = $em->getRepository(Mensaje::class);
        $usuarioRepository = $em->getRepository(Usuario::class);
        $json = json_decode($request->getContent(), true);

        //Obtenemos los parametros
        $token = $request->headers->get('token');
        $valido = $utils->esApiKeyValida($token, null);
        if (!$valido) {

            return $this->json(['message' => "El token de sesion ha caducado"], 400);

        } else {

            if (!$json['texto']) {
                return $this->json(['message' => "No ha rellenado los campos requeridos"], 400);
            }

            $texto = $json['texto'];
            $usuario_id = Token::getPayload($token)['user_id'];
            $receptor = $json['receptor'];
            date_default_timezone_set('Europe/Madrid');
            $fecha = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

            $emisor = $usuarioRepository->findOneBy(array('id' => $usuario_id));

            if ($emisor) {

                $receptor_id = $usuarioRepository->findOneBy(array('id' => $receptor));

                $newMensaje = new Mensaje();

                if($receptor){
                    $newMensaje->setTexto($texto);
                    $newMensaje->setReceptor($receptor_id);
                    $newMensaje->setFecha($fecha);
                    $newMensaje->setEmisor($emisor);

                    $mensajeRepository->save($newMensaje, true);
                } else{
                    return $this->json(['message' => "No existe el receptor"]);
                }

            } else {
                return $this->json(['message' => "No existe este usuario"]);
            }
        }

        return $this->json(['message' => "Mensaje enviado"]);
    }


    #[Route('/api/publicacion/listar/poreceptor', name: 'app_publicacion_listarreceptor', methods: ['GET'])]
    #[OA\Tag(name: 'Publicación')]
    public function listarPorReceptor(Utils $utils, Request $request,ConvertersDTO $converters): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $mensajeRepository = $em->getRepository(Mensaje::class);

        //Obtenemos los parametros
        $token = $request->headers->get('token');
        $valido = $utils->esApiKeyValida($token, null);

        if (!$valido) {
            return $this->json(['message' => "El token de sesion ha caducado"], 400);
        } else {
            $usuario_id = Token::getPayload($token)['user_id'];

            $listaReceptor = $mensajeRepository->findByReceptor($usuario_id);
            if($listaReceptor){

                return $this->mensajeToJson($listaReceptor, $converters, $utils);
            } else {
                return $this->json(['message' => "No hay mensaje"], 400);
            }


        }

    }


    // METODO EXTRAIDO QUE CONVIERTE LAS LISTAS DE PUBLICACIONES A JSON

    /**
     * @param mixed $listaMensajes
     * @param ConvertersDTO $converters
     * @param Utils $utilidades
     * @return JsonResponse
     */
    public function mensajeToJson(mixed $listaMensajes, ConvertersDTO $converters, Utils $utilidades): JsonResponse
    {
        $listJson = array();

        foreach ($listaMensajes as $mensaje) {

            $mensajeDto = $converters->mensajetoDTO($mensaje);
            $mensajeDto->setFecha($mensaje->getFecha()->format('Y-m-d H:i:s'));

            $json = $utilidades->toJson($mensajeDto, null);
            $listJson[] = json_decode($json, true);
        }

        return new JsonResponse($listJson, 200, [], false);
    }




    //Eliminar mensaje

    #[Route('/api/mensaje/delete', name: 'app_mensaje_borrar', methods: ["GET"])]
    #[OA\Tag(name: 'Mensaje')]
    public function eliminar(Request $request): JsonResponse
    {

        //Autowireds
        $em = $this->doctrine->getManager();
        $mensajeRepository = $em->getRepository(Mensaje::class);

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);
        $mensajeId = $json['id'];
        $mensaje = $mensajeRepository->findOneBy(array('id' => $mensajeId));

        if ($mensaje != null) {

            if ($mensajeId == $mensaje->getId()) {
                $mensajeRepository->remove($mensaje, true);
                return new JsonResponse("{ mensaje: Mensaje ha sido eliminado correctamente }", 200, [], true);

            }

        }

        return new JsonResponse("{ mensaje: El mensaje que intenta eliminar no existe }", 409, [], true);
    }
}