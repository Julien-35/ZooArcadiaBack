<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
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



#[Route('api/avis', name:'app_api_arcadia_avis_')]

class AvisController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $manager,
        public AvisRepository $repository,
        public SerializerInterface $serializer,
        public UrlGeneratorInterface $urlGenerator,
    )
    { 
}

    #[Route('/post',name:'avis', methods:['POST'])]
    #[OA\Post(
        path:"/api/avis/post",
        summary:"Créer un nouvel avis",
        requestBody : new RequestBody(
            required: true,
            description : "Pour créer un nouvel avis, suivez les informations ci-dessous",
            content : new Mediatype(mediaType: "application/json",
                schema : new Schema (type: "object", properties:[
                    new Property (
                        property: "pseudo",
                        type : 'string',
                        example :'DUDU'
                    ),
                    new Property (
                        property: "commentaire",
                        type : 'string',
                        example :'test commentaire'
                    ),
                    new Property (
                        property: "isVisible",
                        type : 'bool',
                        example : true
                    ),
                ])
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Création de l'avis",
        content: new OA\JsonContent(
            type: 'string',
        )
    )]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                throw new \Exception('Invalid JSON data');
            }
    
            $avis = new Avis();
            $avis->setPseudo($data['pseudo']);
            $avis->setCommentaire($data['commentaire']);
            $avis->setIsVisible($data['is_visible']);
    
            $entityManager->persist($avis);
            $entityManager->flush();
    
            $responseData = $serializer->serialize($avis, 'json');
    
            return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/get', name:'show', methods:['GET'])]
    public function show(): JsonResponse 
    { 
        $avis = $this->repository->findAll();
        $responseData = $this->serializer->serialize($avis, 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    } 

    #[Route('/{id}', name: 'edit', methods:['PUT'])]
    public function edit(int $id,Request $request): Response
    { 
        $avis = $this->repository->findOneBy(['id' => $id]);
        if ($avis) {
            $avis = $this->serializer->deserialize(
                $request->getContent(),
                Avis::class,
                    'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $avis]
            );
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    
    #[Route('/{id}', name:'delete', methods:['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $avis = $this->repository->findOneBy(['id' => $id]);
        if ($avis) {
            $this->manager->remove($avis);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}

