<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
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

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

#[Route('api/avis', name:'app_api_arcadia_avis_')]
#[OA\Post(
    path:"/api/avis",
    summary:"Ajouter un avis ",
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

class AvisController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AvisRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){


        
    }

    #[Route('/post',name:'avis')]
    public function new(Request $request): JsonResponse

    
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

        $avis = $this->serializer->deserialize($request->getContent(), Avis::class, 'json');
        $this->manager->persist($avis);
        $this->manager->flush();

        return new JsonResponse([
            'pseudo'  => $avis->getPseudo(),
            'commentaire' => $avis->getCommentaire(),
            'is_visible' => $avis->isIsVisible(),
        ],
            Response::HTTP_CREATED
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
        $avis = $this->repository->findAll();
        $responseData = $this->serializer->serialize($avis, 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    } 

    #[Route('/{id}', name: 'edit')]
    public function edit(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $avis = $this->repository->findOneBy(['id' => $id]);
        if (!$avis) {
            throw $this->createNotFoundException("Aucun avis trouvé pour {$id} id");
        }
        $avis->setAvis($data);
        $this->manager->flush();
        return $this->redirectToRoute('app_api_arcadia_avis_show', ['id' => $avis->getId()]);
    }


    public function delete(int $id): Response
    {
        $avis = $this->repository->findOneBy(['id' => $id]);
        if (!$avis) {
            throw $this->createNotFoundException("Aucun avis trouvé {$id} id");
        }
        $this->manager->remove($avis);
        $this->manager->flush();
        return $this->json(['message' => "L'avis a été supprimé."], Response::HTTP_NO_CONTENT);
    }
}