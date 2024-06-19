<?php

namespace App\Controller;

use App\Entity\Horaire;
use DateTimeImmutable ;
use App\Repository\HoraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;


#[Route('api/horaire', name:'app_api_arcadia_horaire_')]
class HoraireController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HoraireRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){

    }

    #[Route(methods:'POST')]
    #[OA\Post(
        path:"/api/horaire",
        summary:"Ajouter un horaire ",
        requestBody : new RequestBody(
        required: true,
        description : "Indiquer les informations concernants l'horaire",
        content : [new Mediatype(mediaType: "application/json",
        schema : new Schema (type: "object", properties:[
        new Property (
            property: "Prénom",
            type : 'string',
            example :'Choukette'
        ),
        new Property (
            property: "Etat de l'horaire",
            type : "string",
            example : "Ne mange plus. Attention à surveiller"
        )
]))]

        ),
    )]
    #[OA\Response(
        response: 200,
        description: "Création de l'horaire",
        content: new OA\JsonContent(
            type: 'string',
        )
    )]
    public function new(Request $request): JsonResponse

        {
            $horaire = $this->serializer->deserialize($request->getContent(), Horaire::class, 'json');
    
            $this->manager->persist($horaire);
            $this->manager->flush();
            
            $responseData = $this->serializer->serialize($horaire,'json');
            
            $location = $this->urlGenerator->generate(
                'app_api_arcadia_horaire_show',
                ['id' => $horaire->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
            return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);
        }


    #[Route('/get',name:'show')]
    public function show(): JsonResponse 
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
            exit(0);
        }
        $horaire = $this->repository->findAll();
        $responseData = $this->serializer->serialize($horaire, 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    } 




    
    #[Route('/{id}', name:'edit')]
    public function edit(int $id, Request $request): JsonResponse
    {  if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }
        $horaire = $this->repository->findOneBy(['id' => $id]);
        if ($horaire) {
            $horaire = $this->serializer->deserialize(
                $request->getContent(),
                Horaire::class,
                    'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $horaire]
            );
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}',name:'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $horaire = $this->repository->findOneBy(['id' => $id]);
        if ($horaire) {
            $this->manager->remove($horaire);
            $this->manager->flush();

            return new JsonResponse(NULL, Response ::HTTP_NO_CONTENT);
        }
        return new JsonResponse(NULL, Response ::HTTP_NOT_FOUND);
    }
}
