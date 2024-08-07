<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
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



#[Route('api/service', name:'app_api_arcadia_service_')]
class ServiceController extends AbstractController
{
    
    public function __construct(
        private EntityManagerInterface $manager,
        private ServiceRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    #[Route('/post', name:'app_api_arcadia_service_', methods : ['POST'])]
    #[OA\Post(
        path:"/test",
        summary:"Ajouter un service ",
        requestBody : new RequestBody(
        required: true,
        description : "Indiquer la description du service",
        content : [new Mediatype(mediaType: "application/json",
        schema : new Schema (type: "object", properties:[
        new Property (
            property: "nom",
            type : 'string',
            example :'Service de restauration'
        ),
        new Property (
            property: "description",
            type : "string",
            example : "Plusieurs services de restaurations sont disponibles partout sur le parc"
        )
]))]


        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Création du service',
        content: new OA\JsonContent(
            type: 'string',
        )
    )]

    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $service = new Service();
        $service->setNom($data['nom']);
        $service->setDescription($data['description']);

        $entityManager->persist($service);
        $entityManager->flush();

        $responseData = $serializer->serialize($service, 'json');

        return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
    }


    #[Route('/get', name: 'show', methods:['GET'])]
    #[OA\Get(
        path:"/api/service/{id}",
        summary:"Voir un service depuis son id",
    
    )]
    #[OA\Response(
        response: 200,
        description: 'Retourner le service via son ID',
        content: new OA\JsonContent(
            type: 'string',
        )
    )]

    #[OA\Parameter(
        name: 'nom du service',
        in: 'query',

    )]


    public function show(): JsonResponse 
    {
        $service = $this->repository->findAll();
        $responseData = $this->serializer->serialize($service, 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    } 


    #[Route('/{id}', name:'edit', methods : ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    { 
        $service = $this->repository->findOneBy(['id' => $id]);
        if ($service) {
            $service = $this->serializer->deserialize(
                $request->getContent(),
                Service::class,
                    'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $service]
            );
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}',name:'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $service = $this->repository->findOneBy(['id' => $id]);
        if ($service) {
            $this->manager->remove($service);
            $this->manager->flush();

            return new JsonResponse(NULL, Response ::HTTP_NO_CONTENT);
        }
        return new JsonResponse(NULL, Response ::HTTP_NOT_FOUND);
    }
}


