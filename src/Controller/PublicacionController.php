<?php

namespace App\Controller;

use App\dto\ConvertersDTO;
use App\Entity\Archivo;
use App\Entity\Comunidad;
use App\Entity\Publicacion;
use App\Entity\PublicacionTags;
use App\Entity\Tags;
use App\Entity\Usuario;
use App\Repository\PublicacionRepository;
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

class PublicacionController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    #[Route('/api/publicacion/crear', name: 'app_publicacion_crear', methods: ['POST'])]
    #[OA\Tag(name: 'Publicación')]
    #[Security(name: "apiKey")]
    public function crear(Request $request, Utils $utils): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $publicacionRepository = $em->getRepository(Publicacion::class);
        $usuarioRepository = $em->getRepository(Usuario::class);
        $comunidadRepository = $em->getRepository(Comunidad::class);
        $tagRepository = $em->getRepository(Tags::class);
        $publicacionTagRepository = $em->getRepository(PublicacionTags::class);

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);

        //Obtenemos los parametros
        $token = $request->headers->get('token');
        $valido = $utils->esApiKeyValida($token, null);
        if (!$valido){

            return $this->json(['message' => "El token de sesion ha caducado"], 400);

        } else {

            if (!$json['texto'] or !$json['titulo']){
                return $this->json(['message' => "No ha rellenado los campos requeridos"], 400);
            }

            $usuario_id = Token::getPayload($token)['user_id'];
            $tagNombre = $json['etiqueta'];
            $comunidadNombre = $json['comunidad'];
            $titulo = $json['titulo'];
            $texto = $json['texto'];
            $link = $json['link'];

            date_default_timezone_set('Europe/Madrid');
            $fecha =  DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

            //Sacamos las entidades que se indican dentro de publicacion (Si es null, es que se publica en su perfil)
            $usuario = $usuarioRepository->findOneBy(array('id'=>$usuario_id));

            if ($comunidadNombre){
                $comunidad = $comunidadRepository->findOneBy(array('nombre'=> $comunidadNombre));
            } else {
                $comunidad = false;
            }

            $archivo = false;

            //Comprobamos que no exista una publicacion exactamente igual
            $posiblePublicacion = $publicacionRepository->findOneBy(array('usuario' => $usuario,
                'titulo' => $titulo, 'texto' => $texto, 'comunidad' => $comunidad));

            if ($posiblePublicacion != null)
            {

                return $this->json(['message' => "Ya existe una publicación igual"], 400);

            } else {
                //Creando la nueva publicacion
                $newPublicacion = new Publicacion();
                $newEtiqueta = new PublicacionTags();
                $newPublicacion->setUsuario($usuario);
                if (!$archivo){
                    $newPublicacion->setArchivo(null);
                } else {
                    $newPublicacion->setArchivo($archivo);
                }
                if (!$comunidad){
                    $newPublicacion->setComunidad(null);
                } else {
                    $newPublicacion->setComunidad($comunidad);
                }
                $newPublicacion->setTitulo($titulo);
                $newPublicacion->setTexto($texto);
                $newPublicacion->setLink($link);
                $newPublicacion->setUpvote(0);
                $newPublicacion->setDownvote(0);
                $newPublicacion->setFechaPublicacion($fecha);

                $tag = $tagRepository->findOneBy(array('nombre' => $tagNombre));
                $newEtiqueta->setPublicacion($newPublicacion);
                $newEtiqueta->setTag($tag);
                //Guardar nueva publicacion
                $publicacionRepository->save($newPublicacion, true);
                $publicacionTagRepository->save($newEtiqueta, true);

                return $this->json(['message' => "Publicación creada correctamente"]);
            }
        }

    }

    //Eliminar publicación sin probar porque no me va el crear
    #[Route('/api/publicacion/eliminar', name: 'app_publicacion_eliminar', methods: ['GET'])]
    #[OA\Tag(name: 'Publicación')]
    public function eliminar(Request $request): JsonResponse
    {
        //Autowireds
        $em = $this-> doctrine->getManager();
        $publicacionRepository = $em->getRepository(Publicacion::class);

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);
        $publicacionid = $json['id'];
        $publicacion = $publicacionRepository->findOneBy(array('id' => $publicacionid));

        if ($publicacion != null)
        {
            if ($publicacionid == $publicacion->getId()){
                $publicacionRepository->remove($publicacion, true);
                return new JsonResponse("{ mensaje: Publicación eliminado correctamente }", 200, [], true);
            }
        }

        return new JsonResponse("{ mensaje: La publicación que intenta eliminar no existe }", 409, [], true);

    }

    //Listar publicaciones por popularidad
    #[Route('/api/publicacion/listar', name: 'app_publicacion_listar', methods: ['GET'])]
    #[OA\Tag(name: 'Publicación')]
    public function listar(Utils $utilidades, ConvertersDTO $converters): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $publicacionRepository = $em->getRepository(Publicacion::class);

        $listaPublicaciones = $publicacionRepository->findAll();


        return $this->publicacionesToJson($listaPublicaciones, $converters, $utilidades);
    }

    //Listar publicaciones por popularidad
    #[Route('/api/publicacion/listar/usuario', name: 'app_publicacion_listarUsuario', methods: ['GET'])]
    #[OA\Tag(name: 'Publicación')]
    public function listarUsuario(Request $request, Utils $utilidades, ConvertersDTO $converters): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $publicacionRepository = $em->getRepository(Publicacion::class);
        $usuarioRepository = $em->getRepository(Usuario::class);
        $comunidadRepository = $em->getRepository(Comunidad::class);

        $token = $request->headers->get('token');
        $idPerfil = $request->headers->get('idPerfil');
        $comunidadNombre = $request->headers->get('comunidad');
        $user_id = Token::getPayload($token)['user_id'];

        if ($idPerfil != null) {
            $user_id = $idPerfil;
        }

        $user = $usuarioRepository->findOneBy(array('id' => $user_id));

        if ($comunidadNombre != null){
            $comunidad = $comunidadRepository->findOneBy(array('nombre' => $comunidadNombre));
            $listaPublicaciones = $publicacionRepository->findBy(array('comunidad' => $comunidad));
        } else {
            $listaPublicaciones = $publicacionRepository->findBy(array('usuario' => $user));
        }

        return $this->publicacionesToJson($listaPublicaciones, $converters, $utilidades);
    }

    #[Route('/api/publicacion/listar/comunidad', name: 'app_publicacion_listarComunidad', methods: ['GET'])]
    #[OA\Tag(name: 'Publicación')]
    public function listarComunidad(Request $request, Utils $utilidades, ConvertersDTO $converters): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $publicacionRepository = $em->getRepository(Publicacion::class);
        $comunidadRepository = $em->getRepository(Comunidad::class);

        $comunidadNombre = $request->headers->get('comunidad');
        $comunidad = $comunidadRepository->findOneBy(array('nombre' => $comunidadNombre));


        $listaPublicaciones = $publicacionRepository->findByFechaComunidad($comunidad);

        return $this->publicacionesToJson($listaPublicaciones, $converters, $utilidades);
    }

    #[Route('/api/publicacion/listar/populares', name: 'app_publicacion_listarPopulares', methods: ['GET'])]
    #[OA\Tag(name: 'Publicación')]
    public function listarPopulares(Utils $utilidades, ConvertersDTO $converters): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $publicacionRepository = $em->getRepository(Publicacion::class);

        $listaPublicaciones = $publicacionRepository->findByRating();


        return $this->publicacionesToJson($listaPublicaciones, $converters, $utilidades);
    }

    #[Route('/api/publicacion/votar', name: 'app_publicacion_votar', methods: ['POST'])]
    #[OA\Tag(name: 'Publicación')]
    public function votar(Request $request): JsonResponse
    {
        // Autowireds
        $em = $this->doctrine->getManager();
        $publicacionRepository = $em->getRepository(Publicacion::class);

        $publicacion_id = $request->headers->get('publicacion');
        $voto = $request->headers->get('voto');

        $publicacion = $publicacionRepository->findOneBy(array('id' => $publicacion_id));
        if (!$publicacion) {
            return $this->json(['message' => "Publicación no ha sido encontrada"]);
        }
        if ($voto == 'upvote') {
            $publicacion->setUpvote($publicacion->getUpvote() + 1);
        } elseif ($voto == 'downvote') {
            $publicacion->setDownvote($publicacion->getDownvote() + 1);
        } else {
            return $this->json(['message' => "Voto inválido"]);
        }

        $em->persist($publicacion);
        $em->flush();

        return $this->json(['message' => "Voto valido"]);
    }



    // METODO EXTRAIDO QUE CONVIERTE LAS LISTAS DE PUBLICACIONES A JSON

    /**
     * @param mixed $listaPublicaciones
     * @param ConvertersDTO $converters
     * @param Utils $utilidades
     * @return JsonResponse
     */
    public function publicacionesToJson(mixed $listaPublicaciones, ConvertersDTO $converters, Utils $utilidades): JsonResponse
    {
        $listJson = array();

        foreach ($listaPublicaciones as $publicacion) {

            $publicacionDto = $converters->publicacionToDto($publicacion);
            $publicacionDto->setFechaPublicacion($publicacion->getFechaPublicacion()->format('Y-m-d H:i:s'));

            $json = $utilidades->toJson($publicacionDto, null);
            $listJson[] = json_decode($json, true);
        }

        return new JsonResponse($listJson, 200, [], false);
    }
}

