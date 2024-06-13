<?php

namespace App\Controller;

use App\Entity\Animal;
use DateTimeImmutable ;
use App\Repository\AnimalRepository;
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


#[Route('api/animal', name:'app_api_arcadia_animal_')]
class AnimalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AnimalRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){

    }

    #[Route(methods:'POST')]
    #[OA\Post(
        path:"/api/animal",
        summary:"Ajouter un animal ",
        requestBody : new RequestBody(
        required: true,
        description : "Indiquer les informations concernants l'animal",
        content : [new Mediatype(mediaType: "application/json",
        schema : new Schema (type: "object", properties:[
        new Property (
            property: "Prénom",
            type : 'string',
            example :'Choukette'
        ),
        new Property (
            property: "Etat de l'animal",
            type : "string",
            example : "Ne mange plus. Attention à surveiller"
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
    public function new(Request $request): JsonResponse

        {
            $animal = $this->serializer->deserialize($request->getContent(), Animal::class, 'json');
    
            $this->manager->persist($animal);
            $this->manager->flush();
            
            $responseData = $this->serializer->serialize($animal,'json');
            
            $location = $this->urlGenerator->generate(
                'app_api_arcadia_service_show',
                ['id' => $animal->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
            return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);
        }


    #[Route('/{prenom}',name:'show', methods: 'GET')]
    public function show(string $prenom): JsonResponse
    {
        $animal = $this->repository->FindOneBy (['prenom'=> $prenom]);
        if ($animal) {
            $responseData = $this->serializer->serialize($animal, 'json');
            return new JsonResponse($responseData, Response ::HTTP_OK,[], true);
        }
        return new JsonResponse(NULL, Response ::HTTP_NOT_FOUND);
    }




    #[Route('/', name:'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $animal = $this->repository->findOneBy(['id' => $id]);
        if ($animal) {
            $animal = $this->serializer->deserialize(
                $request->getContent(),
                    Animal::class,
                    'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $animal]
            );
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}',name:'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $animal = $this->repository->findOneBy(['id' => $id]);
        if ($animal) {
            $this->manager->remove($animal);
            $this->manager->flush();

            return new JsonResponse(NULL, Response ::HTTP_NO_CONTENT);
        }
        return new JsonResponse(NULL, Response ::HTTP_NOT_FOUND);
    }
}
