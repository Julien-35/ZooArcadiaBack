<?php

namespace App\Controller;

use App\Entity\Nourriture;
use App\Repository\NourritureRepository;
use DateTimeImmutable ;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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


#[Route('api/nourriture', name:'app_api_arcadia_nourriture_')]
#[OA\Post(
    path:"/api/nourriture",
    summary:"Ajouter un nourriture ",
    requestBody : new RequestBody(
    required: true,
    description : "Votre message ",
    content : [new Mediatype(mediaType: "application/json",
    schema : new Schema (type: "object", properties:[
    new Property (
        property: "pseudo",
        type : 'string',
        example :'Le rugbyman'
    ),
    new Property (
        property: "commentaire",
        type : "string",
        example : "Le zoo en famille est toujours génial"
    ),

    new Property (
        property: "is_visible",
        type : "boolean",
        example : "true"
    )
]))]

    ),
)]
#[OA\Response(
    response: 200,
    description: "Création de l'animal",
    content: new OA\JsonContent(
        type: 'string',
    )
)]

class NourritureController extends AbstractController
{


    public function __construct(
        public EntityManagerInterface $manager,
        public NourritureRepository $repository,
        public SerializerInterface $serializer,
        public UrlGeneratorInterface $urlGenerator,
    ){
    }

    #[Route('/post',name:'nourriture')]
    public function new(Request $request): JsonResponse
    {  
        if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }

        $nourriture = $this->serializer->deserialize($request->getContent(), Nourriture::class, 'json');
        $this->manager->persist($nourriture);
        $this->manager->flush();

            return new JsonResponse([
                'date'  => $nourriture->getDate(),
                'nourriture' => $nourriture->getNourriture(),
                'grammage' => $nourriture->getGrammage(),
            ],
        );
    }



    #[Route('/get', name: 'show')]
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
        $nourriture = $this->repository->findAll();
        $responseData = $this->serializer->serialize($nourriture, 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    } 

    #[Route('/{id}', name: 'edit')]
    public function edit(int $id,Request $request): Response
    { if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    
    }
        $nourriture = $this->repository->findOneBy(['id' => $id]);
        if ($nourriture) {
            $nourriture = $this->serializer->deserialize(
                $request->getContent(),
                Nourriture::class,
                    'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $nourriture]
            );
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }




//     public function delete(int $id): Response
//     {
//         $nourriture = $this->repository->findOneBy(['id' => $id]);
//         if (!$nourriture) {
//             throw $this->createNotFoundException("Aucun nourriture trouvé {$id} id");
//         }
//         $this->manager->remove($nourriture);
//         $this->manager->flush();
//         return $this->json(['message' => "L'nourriture a été supprimé."], Response::HTTP_NO_CONTENT);
//     }
}

