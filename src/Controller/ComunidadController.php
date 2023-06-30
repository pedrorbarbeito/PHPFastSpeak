<?php

namespace App\Controller;

use App\dto\ConvertersDTO;
use App\Entity\Comunidad;
use App\Entity\Usuario;
use App\Utilidades\Utils;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use ReallySimpleJWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ComunidadController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    #[Route('/api/comunidad/crear', name: 'app_comunidad_crear', methods: ['POST'])]
    #[OA\Tag(name: 'Comunidad')]
    #[Security(name: "apiKey")]
    public function crear(Request $request, Utils $utils): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $comunidadRepository = $em->getRepository(Comunidad::class);
        $usuarioRepository = $em->getRepository(Usuario::class);

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);

        //Obtenemos los parametros
        $token = $request->headers->get('token');
        $valido = $utils->esApiKeyValida($token, null);
        if (!$valido){ //creo que hay que cambiarlo
            return $this->json(['message' => "El token de sesion ha caducado"]);
        } else {
            $usuario_id = Token::getPayload($token)['user_id'];
            $nombre = $json['nombre'];
            $tipo = $json['tipo'];
            $imagen = $json['imagen'];
            $banner = $json['banner'];

            //Sacamos el usuario propietario de la comunidad
            $usuario = $usuarioRepository->findOneBy(array('id'=>$usuario_id));

            //Comprobamos que no exista una comunidad igual
            $posibleComunidad = $comunidadRepository->findOneBy(array('usuario'=> $usuario,
                'nombre' => $nombre, 'tipo' => $tipo));

            if ($posibleComunidad != null)
            {
                return $this->json(['message' => "Ya existe la comunidad indicada"]);

            } else {
                //Creando la nueva publicacion
                $newComunidad = new Comunidad();
                $newComunidad->setUsuario($usuario);
                $newComunidad->setTipo($tipo);
                $newComunidad->setNombre($nombre);
                $newComunidad->setBanner($banner);

                if ($imagen == null){
                    $newComunidad->setImagen("https://img.myloview.com/stickers/default-avatar-profile-icon-vector-social-media-user-700-202768327.jpg");
                } else {
                    $newComunidad->setImagen($imagen);
                }


                // Falta añadir el fondo

                //Guardar nueva publicacion
                $comunidadRepository->save($newComunidad, true);

                return $this->json(['message' => "Comunidad creada correctamente"]);
            }
        }
    }

    //REVISAR
    #[Route('/api/comunidad/eliminar', name: 'app_comunidad_eliminar', methods: ['GET'])]
    #[OA\Tag(name: 'Comunidad')]
    public function eliminar(Request $request): JsonResponse
    {
        //Autowireds
        $em = $this-> doctrine->getManager();
        $publicacionRepository = $em->getRepository(Comunidad::class);

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);
        $comunidad_id = $json['id'];
        $comunidad = $publicacionRepository->findOneBy(array('id' => $comunidad_id));

        if ($comunidad != null)
        {
            if ($comunidad_id == $comunidad->getId()){
                $publicacionRepository->remove($comunidad, true);
                return new JsonResponse("{ mensaje: Comunidad eliminado correctamente }", 200, [], true);
            }
        }

        return new JsonResponse("{ mensaje: La comunidad que intenta eliminar no existe }", 409, [], true);

    }


    //LISTAR COMUNIDADES
    #[Route('/api/comunidad/listar', name: 'app_comunidad_listar', methods: ['GET'])]
    #[OA\Tag(name: 'Comunidad')]
    public function listar(Utils $utilidades, ConvertersDTO $converters): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $comunidadRepository = $em->getRepository(Comunidad::class);

        $listaComunidades = $comunidadRepository->findAll();


        return $this->comunidadToJson($listaComunidades, $converters, $utilidades);
    }

    //Buscar comunidad

    #[Route('/api/comunidad', name: 'app_comunidad_buscarComunidad', methods: ['GET'])]
    #[OA\Tag(name: 'Comunidad')]
    public function buscarComunidad(Utils $utilidades, ConvertersDTO $converters, Request $request): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $comunidadRepository = $em->getRepository(Comunidad::class);

        $comunidad = $comunidadRepository->findBy(array("nombre"=>$request->headers->get('comunidad')));

        if($comunidad){

            return $this->comunidadToJson($comunidad, $converters, $utilidades);

        }else{
            return $this->json(['message' => "No existe esa comunidad"], 400);
        }
    }

    /**
     * @param mixed $listaComunidades
     * @param ConvertersDTO $converters
     * @param Utils $utilidades
     * @return JsonResponse
     */

    public function comunidadToJson(mixed $listaComunidades, ConvertersDTO $converters, Utils $utilidades): JsonResponse
    {
        $listJson = array();

        foreach ($listaComunidades as $comunidad) {

            $comunidadDTO = $converters->comunidadToDto($comunidad);

            $json = $utilidades->toJson($comunidadDTO, null);
            $listJson[] = json_decode($json, true);
        }

        return new JsonResponse($listJson, 200, [], false);
    }
}


