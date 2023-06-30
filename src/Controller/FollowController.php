<?php

namespace App\Controller;

use App\Entity\Comunidad;
use App\Entity\Follow;
use App\Entity\Usuario;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use ReallySimpleJWT\Token;

class FollowController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->doctrine = $managerRegistry;
    }

    #[Route('/api/follow/seguir', name: 'app_follow_seguir', methods: ['POST'])]
    #[OA\Tag(name: 'Follow')]
    public function seguir(Request $request): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $usuarioRepository = $em->getRepository(Usuario::class);
        $followRepository = $em->getRepository(Follow::class);
        $comunidadRepository = $em->getRepository(Comunidad::class);

        //Obtenemos los parametros
        $token = $request->headers->get('token');

        $emisor_id = Token::getPayload($token)['user_id'];;
        $receptor_id = $request->headers->get('followUsuario');
        $comunidadId = $request->headers->get('followComunidad');

        $emisor = $usuarioRepository->findOneBy(array('id' => $emisor_id));
        if ($receptor_id != "null") {
            $receptor = $usuarioRepository->findOneBy(array('id' => $receptor_id));
        } else {
            $receptor = null;
        }

        if ($comunidadId != "null"){
            $comunidad = $comunidadRepository->findOneBy(array('id' => $comunidadId));
        } else {
            $comunidad = null;
        }

        if ($comunidad == null and $receptor == null){
            return $this->json(['message' => "La comunidad o el Usuario no existe"]);
        }

        $followReceptor = $followRepository->findOneBy(array('emisor' => $emisor, 'receptor' => $receptor, 'comunidad' => null));
        $followComunidad = $followRepository->findOneBy(array('emisor' => $emisor, 'comunidad' => $comunidad, 'receptor' => null));

        if ($emisor != null) {
            if ($receptor == null and $followComunidad == null and $comunidad != null) {
                $following = new Follow();
                $following->setEmisor($emisor);
                $following->setComunidad($comunidad);

                $followRepository->save($following, true);

                return $this->json(['message' => "Comunidad seguida correctamente"]);

            } elseif ($followReceptor == null and $receptor != null) {

                $following = new Follow();
                $following->setEmisor($emisor);
                $following->setReceptor($receptor);

                $followRepository->save($following, true);

                return $this->json(['message' => "Usuario seguido correctamente"]);

            } else {
                return $this->json(['message' => "No existe o ya le sigues"]);
            }
        }
        return $this->json(['message' => "No existe este usuario"]);
    }

    #[Route('/api/follow/unfollow', name: 'app_follow_unfollow', methods: ['POST'])]
    #[OA\Tag(name: 'Follow')]
    public function unfollow(Request $request): JSONResponse {
        //Autowireds
        $em = $this->doctrine->getManager();
        $usuarioRepository = $em->getRepository(Usuario::class);
        $followRepository = $em->getRepository(Follow::class);
        $comunidadRepository = $em->getRepository(Comunidad::class);

        //Obtenemos los parametros
        $token = $request->headers->get('token');

        $emisor_id = Token::getPayload($token)['user_id'];;
        $receptor_id = $request->headers->get('followUsuario');
        $comunidadId = $request->headers->get('followComunidad');

        $emisor = $usuarioRepository->findOneBy(array('id' => $emisor_id));
        if ($receptor_id != "null") {
            $receptor = $usuarioRepository->findOneBy(array('id' => $receptor_id));
        } else {
            $receptor = null;
        }

        if ($comunidadId != "null"){
            $comunidad = $comunidadRepository->findOneBy(array('id' => $comunidadId));
        } else {
            $comunidad = null;
        }

        $followReceptor = $followRepository->findOneBy(array('emisor' => $emisor, 'receptor' => $receptor, 'comunidad' => null));
        $followComunidad = $followRepository->findOneBy(array('emisor' => $emisor, 'comunidad' => $comunidad, 'receptor' => null));

        if ($emisor != null) {
            if ($followComunidad != null) {
                $followRepository->remove($followComunidad,  true);

                return $this->json(['message' => "Unfollow Comunidad"]);

            } elseif ($followReceptor != null) {
                $followRepository->remove($followReceptor,  true);

                return $this->json(['message' => "Unfollow Usuario"]);

            } else {
                return $this->json(['message' => "No existe o no lo sigues"]);
            }
        }
        return $this->json(['message' => "No existe este usuario"]);
    }

    #[Route('/api/follow/check', name: 'app_follow_check', methods: ['GET'])]
    #[OA\Tag(name: 'Follow')]
    public function chequear(Request $request): JSONResponse {
        //Autowireds
        $em = $this->doctrine->getManager();
        $usuarioRepository = $em->getRepository(Usuario::class);
        $followRepository = $em->getRepository(Follow::class);
        $comunidadRepository = $em->getRepository(Comunidad::class);

        //Obtenemos los parametros
        $token = $request->headers->get('token');

        $emisor_id = Token::getPayload($token)['user_id'];
        $receptor_id = $request->headers->get('followUsuario');
        $comunidad = $request->headers->get('comunidad');

        $emisor = $usuarioRepository->findOneBy(array('id' => $emisor_id));
        if ($receptor_id != "null") {
            $receptor = $usuarioRepository->findOneBy(array('id' => $receptor_id));
        } else {
            $receptor = null;
        }

        if ($comunidad != "null"){
            $comunidad = $comunidadRepository->findOneBy(array('nombre' => $comunidad));
        } else {
            $comunidad = null;
        }


        $followReceptor = $followRepository->findOneBy(array('emisor' => $emisor, 'receptor' => $receptor, 'comunidad' => null));
        $followComunidad = $followRepository->findOneBy(array('emisor' => $emisor, 'comunidad' => $comunidad, 'receptor' => null));

        if ($emisor != null) {
            if (!$followComunidad and !$followReceptor){
                return $this->json(['message' => "No lo sigues"]);
            }

            if ($followReceptor == $followComunidad){
                if ($followReceptor->getComunidad() != null){
                    return $this->json(['message' => "Sigues a esta comunidad"]);
                } else {
                    return $this->json(['message' => "Sigues a este usuario"]);
                }
            }


            if ($followReceptor != null) {
                return $this->json(['message' => "Sigues a este usuario"]);
            } elseif ($followComunidad != null){
                return $this->json(['message' => "Sigues a esta comunidad"]);
            }

        }
        return $this->json(['message' => "No existe este usuario"]);
    }

    //Sacar numero de seguidores tanto perfil como comunidad
    #[Route('/api/follow/seguidores', name: 'app_follow_seguidores', methods: ['GET'])]
    #[OA\Tag(name: 'Follow')]
    public function seguidores(Request $request): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $usuarioRepository = $em->getRepository(Usuario::class);
        $followRepository = $em->getRepository(Follow::class);
        $comunidadRepository = $em->getRepository(Comunidad::class);

        //Obtenemos los parametros
        $usuarioNombre = $request->headers->get('usuarioPerfil');
        $comunidadNombre = $request->headers->get('comunidad');

        if ($usuarioNombre != "null") {
            $usuario = $usuarioRepository->findOneBy(array('username' => $usuarioNombre));
        } else {
            $usuario = null;
        }

        if ($comunidadNombre != "null"){
            $comunidad = $comunidadRepository->findOneBy(array('nombre' => $comunidadNombre));
        } else {
            $comunidad = null;
        }


        $seguidores = array();

        //Sacamos el numero de seguidores que tiene el usuario o la comunidad
        if ($usuario != null){
            $seguidores = $followRepository->findBy(array('receptor' => $usuario));
        } else{
            $seguidores = $followRepository->findBy(array('comunidad' => $comunidad));
        }

        return $this->json(['message' => count($seguidores)]);
    }



    //Sacar numero de personas que sigues
    #[Route('/api/follow/seguidos', name: 'app_follow_seguidos', methods: ['GET'])]
    #[OA\Tag(name: 'Follow')]
    public function seguidos(Request $request): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $usuarioRepository = $em->getRepository(Usuario::class);
        $followRepository = $em->getRepository(Follow::class);

        //Obtenemos los parametros
        $usuarioNombre = $request->headers->get('usuarioPerfil');
        $perfilID = $request->headers->get('idPerfil');
        $token = $request->headers->get('token');

        if ($usuarioNombre != "null" and $perfilID == "null") {
            $usuario = $usuarioRepository->findOneBy(array('username' => $usuarioNombre));
        } elseif ($usuarioNombre == "null" and $perfilID != "null") {
            $usuario = $usuarioRepository->findOneBy(array('id' => $perfilID));
        } else {
            $usuario_id = Token::getPayload($token)['user_id'];
            $usuario = $usuarioRepository->findOneBy(array('id' => $usuario_id));
        }

        $seguidos = array();

        //Sacamos el numero de seguidores que tiene el usuario o la comunidad
        if ($usuario != null) {
            $seguidos = $followRepository->findBy(array('emisor' => $usuario));
        }

        return $this->json(['message' => count($seguidos)]);
    }
}