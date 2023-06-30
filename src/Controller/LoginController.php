<?php

namespace App\Controller;

use App\Entity\ApiKey;
use App\Entity\Usuario;
use App\Utilidades\Utils;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;

class LoginController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->doctrine = $managerRegistry;
    }


    #[Route('/api/login', name: 'app_login', methods: ["POST"])]
    public function login(Request $request, Utils $utils): JsonResponse
    {
        //Cargar repositorios
        $em = $this->doctrine->getManager();
        $userRepository = $em->getRepository(Usuario::class);
        $apikeyRepository = $em->getRepository(ApiKey::class);


        //Cargar datos del cuerpo
        $json_body = json_decode($request->getContent(), true);


        //Datos Usuario
        $email = $json_body["email"];
        $password = $json_body["password"];

        //Validar que los credenciales son correctos
        if ($email != null and $password != null) {

            $user = $userRepository->findOneBy(array("email" => $email));


            if ($user != null) {

                $verify = $utils->verify($password, $user->getPassword());

                if ($verify) {

                    $apikey = $apikeyRepository->findOneBy(array('usuario' => $user));

                    $totalTokens = $apikeyRepository->findBy(array('usuario' => $user));

                    if (count($totalTokens) != 1) {
                        foreach ($totalTokens as $token) {
                            $apikeyRepository->remove($token, true);
                        }
                    }

                    if ($apikey == null) {

                        $utils->generateApiToken($user);

                    } else {
                        $valido = $utils->esApiKeyValida($apikey->getToken(), null);

                        if (!$valido) {
                            $tokenNuevo = $apikeyRepository->findOneBy(array('usuario' => $user));
                            return $this->json(['token' => $tokenNuevo->getToken()]);

                        } else {
                            $token = $apikeyRepository->findOneBy(array('usuario' => $user));

                            return $this->json(['token' => $token->getToken()]);

                        }

                    }

                } else {
                    return $this->json(['message' => "Contraseña no valida"]);
                }

            } else {
                return $this->json(['message' => "Correo no valido"]);
            }

        } else {
            return $this->json(['message' => "No ha rellenado todos los campos"]);
        }

        $token = $apikeyRepository->findOneBy(array('usuario' => $user));
        return $this->json(['token' => $token]);
    }
}