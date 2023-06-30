<?php

namespace App\Controller;

use App\dto\ConvertersDTO;
use App\Entity\Publicacion;
use App\Entity\PublicacionTags;
use App\Entity\Tags;
use App\Utilidades\Utils;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->doctrine = $managerRegistry;
    }


    #[Route('/api/etiqueta/crear', name: 'app_etiqueta_crear', methods: ['POST'])]
    #[OA\Tag(name: 'Tags')]
    public function crear(Request $request): JsonResponse
    {
        //Autowireds
        $em = $this->doctrine->getManager();
        $tagsRepository = $em->getRepository(Tags::class);

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);

        //Obtenemos los parametros
        $nombre = $json['nombre'];
        $descripcion = $json['descripcion'];


        if ($nombre != null and $descripcion != null) {
            //Comprobamos que no exista una comunidad igual
            $posibleRepetida = $tagsRepository->findOneBy(array('nombre' => $nombre));
            if ($posibleRepetida != null) {
                return new JsonResponse("{ mensaje: Ya existe una etiqueta igual}", 409, [], true);
            } else {
                $newTag = new Tags();
                $newTag->setNombre($nombre);
                $newTag->setDescripcion($descripcion);

                $tagsRepository->save($newTag, true);
            }

            return new JsonResponse("{ Etiqueta creada }", 200, [], true);
        } else {
            return new JsonResponse("{ Etiqueta no creada correctamente}", 101, [], true);
        }
    }

    #[Route('/api/etiqueta/eliminar', name: 'app_etiqueta_eliminar', methods: ['POST'])]
    #[OA\Tag(name: 'Tags')]
    public function eliminar(Request $request):JSONResponse {

        //Autowireds
        $em = $this->doctrine->getManager();
        $tagsRepository = $em->getRepository(Tags::class);

        //Obtener Json del body
        $json = json_decode($request->getContent(), true);
        $tagId = $json['id'];
        $tag = $tagsRepository->findOneBy(array('id' => $tagId));

            if ($tag != null)
            {
                if ($tagId == $tag->getId()){
                    $tagsRepository->remove($tag, true);
                    return new JsonResponse("{ mensaje: Etiqueta ha sido  eliminada correctamente }", 200, [], true);
                }
            }

            return new JsonResponse("{ mensaje: La etiqueta que intenta eliminar no existe }", 409, [], true);

        }

    #[Route('/api/publicacion/listartags', name: 'app_publicacion_listartags', methods: ['GET'])]
    #[OA\Tag(name: 'Tags')]
    public function listarTags(Utils $utilidades, ConvertersDTO $converters): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $tagsRepository = $em->getRepository(Tags::class);

        $listaTags = $tagsRepository->findAll();

        return $this->tagsToJson($listaTags, $converters, $utilidades);
    }

    /**
     * @param mixed $listaTags
     * @param ConvertersDTO $converters
     * @param Utils $utilidades
     * @return JsonResponse
     */
    public function tagsToJson(mixed $listaTags, ConvertersDTO $converters, Utils $utilidades): JsonResponse
    {
        $listJson = array();

        foreach ($listaTags as $tags) {

            $tagsDto = $converters->tagToDto($tags);

            $json = $utilidades->toJson($tagsDto, null);
            $listJson[] = json_decode($json, true);
        }

        return new JsonResponse($listJson, 200, [], false);
    }

    #[Route('/api/publicacion/agregarE', name: 'app_tag_agregar', methods: ['GET'])]
    #[OA\Tag(name: 'Tags')]
    public function agregarEtiquetas(Request $request): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $publicacionRepository = $em->getRepository(Publicacion::class);
        $tagsRepository = $em->getRepository(Tags::class);
        $tagsPublicacionRepository = $em->getRepository(PublicacionTags::class);
        $json = json_decode($request->getContent(), true);
        $nombre = $json['nombre'];
        $publicacion = $publicacionRepository->find($request->headers->get('publicacionId'));
        $tag = $tagsRepository->findOneBy(array('nombre' => $nombre));
        $tagsPublicacion = $tagsPublicacionRepository->findOneBy(array('publicacion' => $publicacion, 'tag' => $tag));

        if (!$publicacion) {
            return new JsonResponse(['error' => 'La publicación no existe'], 404);
        } elseif (!$tag) {
            return new JsonResponse(['error' => 'La etiqueta no existe'], 404);
        } elseif (!$tagsPublicacion) {

            $tagsPublicacion = new PublicacionTags();
            $tagsPublicacion->setPublicacion($publicacion);
            $tagsPublicacion->setTag($tag);
            $em->persist($tagsPublicacion);

            return new JsonResponse(['mensaje' => 'Añadida'], 200);
        }

        $em->flush();
        return new JsonResponse(['mensaje' => 'Añadida'], 200);
    }

    #[Route('/api/tags/publicacion', name: 'app_tags_publicacion', methods: ['GET'])]
    #[OA\Tag(name: 'Tags')]
    public function tagsPublicacion(Utils $utilidades, ConvertersDTO $converters): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $publicacionTagsRepository = $em->getRepository(PublicacionTags::class);

        $listaTags = $publicacionTagsRepository->findAll();

        $listJson = array();

        foreach ($listaTags as $tags) {

            $tagsDto = $converters->publiTagToDto($tags);

            $json = $utilidades->toJson($tagsDto, null);
            $listJson[] = json_decode($json, true);
        }

        return new JsonResponse($listJson, 200, [], false);
    }

}