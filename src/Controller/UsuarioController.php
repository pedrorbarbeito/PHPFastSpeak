<?php

namespace App\Controller;

use App\dto\ConvertersDTO;
use App\dto\UsuarioDTO;
use App\Entity\ApiKey;
use App\Entity\Comunidad;
use App\Entity\Rol;
use App\Entity\RolUsuarioComunidad;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use App\Utilidades\Utils;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use ReallySimpleJWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class UsuarioController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->doctrine = $managerRegistry;
    }

    #[Route('/api/usuario/perfil', name: 'app_usuario_perfil', methods: ['GET'])]
    #[OA\Tag(name: 'Usuario')]
    public function perfil(Request $request, UsuarioRepository $usuarioRepository, ConvertersDTO $converters): JsonResponse
    {
        //Obtener token desde el header
        $token = $request->headers->get('token');
        $idPerfil = $request->headers->get('idPerfil');
        $usuarioPerfil = $request->headers->get('usuarioPerfil');
        $user_id = Token::getPayload($token)['user_id'];

        if ($idPerfil != null){
            $perfil = $usuarioRepository->findOneBy(array('id' => $idPerfil));
        } else {
            if ($usuarioPerfil != null){
                $perfil = $usuarioRepository->findOneBy(array('username' => $usuarioPerfil));
            }else{
                $perfil = $usuarioRepository->findOneBy(array('id' => $user_id));
            }

        }

        $perfilDto = $converters->usuarioToDto($perfil);
        $perfilDto->setCreatedOn($perfil->getCreatedOn()->format('Y-m-d H:i:s'));

        return $this->json([$perfilDto]);

    }

    #[Route('/api/usuario/list', name: 'app_usuario_listar', methods: ['GET'])]
    #[OA\Tag(name: 'Usuario')]
//    #[OA\Get(description: "Lista de todos los usuarios")]
//    #[OA\Response(response:200,description:"successful operation" ,
//        content: new OA\JsonContent(
//            example: new OA\Examples(
//            example: "user_created", summary: "success", value: "")))]
    public function listar(UsuarioRepository $usuarioRepository, Utils $utilidades, ConvertersDTO $converters): JsonResponse
    {
        $listUsuarios = $usuarioRepository->findAll();

        $listJson = array();

        foreach ($listUsuarios as $user) {

            $usuarioDto = $converters->usuarioToDto($user);
            $usuarioDto->setCreatedOn($user->getCreatedOn()->format('Y-m-d H:i:s'));

            $json = $utilidades->toJson($usuarioDto, null);
            $listJson[] = json_decode($json, true);
        }

        return new JsonResponse($listJson, 200, [], false);
    }

    #[Route('/api/usuario/save', name: 'app_usuario_guardar', methods: ['POST'])]
    #[OA\Tag(name: 'Usuario')]
    //#[OA\RequestBody(description: "Dto del usuario", required: true, content: new OA\JsonContent(ref: new Model(type: UsuarioDTO::class)))]
    #[OA\Response(response: 200, description: "Usuario creado correctamente")]
    #[OA\Response(response: 101, description: "No ha indicado usario y contraseña")]
    public function guardar(Request $request, Utils $utils): JsonResponse
    {

        //Autowireds
        $em = $this->doctrine->getManager();
        $rucRepository = $em->getRepository(RolUsuarioComunidad::class);
        $rolRepository = $em->getRepository(Rol::class);
        $usuarioRepository = $em->getRepository(Usuario::class);


        //Obtener Json del body
        $json = json_decode($request->getContent(), true);

        //Obtenemos los parametros
        $username = $json['username'];
        $password = $json['password'];
        $email = $json['email'];
        $foto=$json['foto'];

        $fechaActual = date("Y-m-d H:i:s");
        $created_on = DateTime::createFromFormat('Y-m-d H:i:s', $fechaActual);


        //Nuevo usuario con los datos del Json
        if ($username != null and $password != null and $email != null) {
            $newUser = new Usuario();
            $newUser->setUsername($username);
            $newUser->setPassword($utils->hashPassword($password));
            $newUser->setCreatedOn($created_on);
            $newUser->setFoto($foto);

            $checkEmail = $usuarioRepository->findOneBy(array('email' => $email));

            if ($checkEmail == null) {
                $newUser->setEmail($email);
            } else {
                return $this->json(['message' => "Ya hay una cuenta creada con el correo indicado"]);
            }

            //Añadimos como rol por defecto al nuevo usuario "USER_GLOBAL", para indicar rol basico sobre la pagina en
            //general
            $ruc = new RolUsuarioComunidad();
            $ruc->setUsuario($newUser);
            $ruc->setRol($rolRepository->findOneBy(array("nombre" => "USER_GLOBAL")));

            $rucRepository->save($ruc);


            //Guardar Nuevo Usuario
            $usuarioRepository->save($newUser, true);

            return $this->json(['message' => "Usuario creado correctamente"]);
        } else {
            return $this->json(['message' => "No ha indicado los campos requeridos"]);
        }
    }


    #[Route('/api/usuario/delete', name: 'app_usuario_borrar', methods: ['DELETE'])]
    #[OA\Tag(name: 'Usuario')]
    public function eliminar(Request $request): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $usuarioRepository = $em->getRepository(Usuario::class);
        $rucRepository = $em->getRepository(RolUsuarioComunidad::class);
        $apikeyRepository = $em->getRepository(ApiKey::class);
        $comunidadRepository = $em->getRepository(Comunidad::class);

        //Obtener Json del body
        $userid = $request->headers->get('id');
        $usuario = $usuarioRepository->findOneBy(array('id' => $userid));
        $rolesRuc = $rucRepository->findBy(array('usuario' => $usuario));
        $apiKey = $apikeyRepository->findOneBy(array('usuario' => $usuario));

        $checkComunidades = $comunidadRepository->findBy(array('usuario' => $usuario));

        if ($usuario != null) {
            if ($checkComunidades != null) {
                foreach ($checkComunidades as $comunidad) {
                    $comunidadRepository->remove($comunidad, true);
                }
            }
            if ($userid == $usuario->getId()) {
                foreach ($rolesRuc as $ruc) {
                    $rucRepository->remove($ruc, true);
                }
                $apikeyRepository->remove($apiKey, true);
                $usuarioRepository->remove($usuario, true);

                return $this->json(['message' => "Usuario eleminiado correctamente"]);
            }
        }

        return $this->json(['message' => "El usuario que intenta eliminar no existe"]);

    }


    //Buscar usuario
    #[Route('/api/usuario/buscarUsuario', name: 'app_usuario_buscarUsuario', methods: ['GET'])]
    #[OA\Tag(name: 'Usuario')]
    public function buscarPorUsuario(Utils $utilidades, ConvertersDTO $converters, Request $request): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $usuarioRepository = $em->getRepository(Usuario::class);

        $usuarios = $usuarioRepository->findBy(array("username"=>$request->headers->get('username')));

        if($usuarios){

            return $this->usuarioToJson($usuarios, $converters, $utilidades);

        }else{
            return $this->json(['message' => "No existe ese usuario"], 400);
        }
    }



    // METODO EXTRAIDO QUE CONVIERTE LAS LISTAS DE PUBLICACIONES A JSON

    /**
     * @param mixed $listaUsuarios
     * @param ConvertersDTO $converters
     * @param Utils $utilidades
     * @return JsonResponse
     */
    public function usuarioToJson(mixed $listaUsuarios, ConvertersDTO $converters, Utils $utilidades): JsonResponse
    {
        $listJson = array();

        foreach ($listaUsuarios as $usuario) {

            $usuarioDto = $converters->usuarioToDTO($usuario);

            $json = $utilidades->toJson($usuarioDto, null);
            $listJson[] = json_decode($json, true);
        }

        return new JsonResponse($listJson, 200, [], false);
    }

    #[Route('/api/usuario/editarPerfil', name: 'app_user_editarperfil', methods: ['PUT'])]
    #[OA\Tag(name: 'Usuario')]
    public function editarPerfil(Utils $utils, ConvertersDTO $converters, Request $request)
    {
        //CARGA DATOS
        $em = $this->doctrine->getManager();
        $usuarioRepository = $em->getRepository(Usuario::class);
        $token = $request->headers->get('token');
        $user_id = Token::getPayload($token)['user_id'];
        $perfil = $usuarioRepository->findOneBy(array('id' => $user_id));

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);

        //Obtenemos los parametros
        $username = $json['username'];
        $password = $json['password'];
        $email = $json['email'];
        $foto = $json['foto'];
        $descripcion = $json['descripcion'];

        if (!$user_id) {
            return $this->json(['message' => "No existe ese usuario"], 400);
        }
        // Decodificar el JSON de la solicitud y crear un DTO
        $dto = new UsuarioDTO();
        $dto->setUsername($username ?? $perfil->getUsername()); //si el nombre es nulo se utilizara el unombre de usuario que ya tenia
        $dto->setPassword($password ?? $perfil->getPassword());
        $dto->setEmail($email ?? $perfil->getEmail());
        $dto->setFoto($foto ?? $perfil->getFoto());
        $dto->setDescripcion($descripcion ?? $perfil->getDescripcion());

        // Actualizar el perfil del usuario
        $perfil->setUsername($dto->getUsername());
        $perfil->setPassword($dto->getPassword());
        $perfil->setPassword($utils->hashPassword($password));
        $perfil->setEmail($dto->getEmail());
        $perfil->setFoto($dto->getFoto());
        $perfil->setDescripcion($dto->getDescripcion());

        $usuarioRepository->save($perfil, true);

        // Retornar una respuesta exitosa
        return $this->json(['message' => "Usuario modificado"]);
    }


    //Sacar ID del usuario que está logueado
    #[Route('/api/usuario/logueado', name: 'app_usuario_logueado', methods: ['GET'])]
    #[OA\Tag(name: 'Usuario')]
    public function usuarioLogueado(Request $request): JsonResponse
    {
        $token = $request->headers->get('token');
        $usuario_id = Token::getPayload($token)['user_id'];

        return $this->json(['message' => $usuario_id]);
    }

}
