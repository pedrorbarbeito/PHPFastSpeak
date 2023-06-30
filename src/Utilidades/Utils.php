<?php

namespace App\Utilidades;

use App\Entity\ApiKey;
use App\Entity\RolUsuarioComunidad;
use App\Entity\Usuario;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use ReallySimpleJWT\Token;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Utils{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this-> doctrine = $managerRegistry;
    }

    //Cambiado a como hace Luis (sino reload)

    public function toJson($data, ?array  $groups ): string
    {
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups("user_query")->toArray();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);


        if($groups != null){
            //Conversion a JSON con groups
            $json = $serializer->serialize($data, 'json', $context);
        }else{
            //Conversion a JSON
            $json = $serializer->serialize($data, 'json');
        }

        return $json;
    }


    public function hashPassword($password): string
    {
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium']
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');

        return $passwordHasher->hash($password);
    }

    public function generateApiToken(Usuario $usuario): void
    {

        //Autowireds
        $em = $this-> doctrine->getManager();
        $rucRepository = $em->getRepository(RolUsuarioComunidad::class);
        $apiKeyRepository = $em->getRepository(ApiKey::class);

        //Genera nueva entidad ApiKey
        $apiKey = new ApiKey();
        $apiKey->setUsuario($usuario);
        $fechaActual10h = date('Y-m-d H:i:s', strtotime('+2 hours'));
        $fecha_expiracion = DateTime::createFromFormat('Y-m-d H:i:s', $fechaActual10h);
        $apiKey->setFechaExpiracion($fecha_expiracion);

        $rolNames = new ArrayCollection();

        foreach ($usuario->getRoles() as $rucs)
        {
            $rolNames[] = $rucs->getRol()->getNombre();
        }

        $tokenData = [
            'user_id' => $usuario->getId(),
            'username' => $usuario->getUsername(),
            'userRol' => $rolNames->getValues(),
            'fecha_expiracion' => $fecha_expiracion

        ];

        $secret = $usuario->getPassword();

        $token = Token::customPayload($tokenData, $secret);
        $apiKey->setToken($token);

        $apiKeyRepository->save($apiKey, true);

    }

    public function esApiKeyValida($token, $permisoRequerido): bool
    {

        //Autowireds
        $em = $this-> doctrine->getManager();
        $apiKeyRepository = $em->getRepository(ApiKey::class);
        $usuarioRepository = $em->getRepository(Usuario::class);

        //Usuario que hace la peticion
        $idUsuario = Token::getPayload($token)['user_id'];

        $apiKey = $apiKeyRepository->findOneBy(array("token" => $token));
        $fechaActual = DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));
        $fechaExpiracion = Token::getPayload($token)['fecha_expiracion'];
        $rolName = Token::getPayload($token)['userRol'];
        $usuario = $usuarioRepository->findOneBy(array("id" => $idUsuario));

        if ( $fechaExpiracion >= $fechaActual){
            $oldToken = $apiKeyRepository->findOneBy(array('usuario' => $usuario));
            $apiKeyRepository->remove($oldToken, true);
            return false;
        } else {
            return $apiKey == null
                or $permisoRequerido == $rolName                    //Preguntar a luis sobre esto
                or $apiKey->getUsuario()->getId() == $idUsuario
                or Token::validate($token, $usuario->getPassword());
        }

    }

    public function  verify($passwordPlain, $passwordBD):bool
    {
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');

        return $passwordHasher->verify($passwordBD,$passwordPlain);

    }

}
