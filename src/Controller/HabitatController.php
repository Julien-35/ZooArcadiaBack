<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
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



#[Route('api/habitat', name:'app_api_arcadia_habitat_')]
class HabitatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){

    }

    #[Route(methods:'POST')]
    public function new(Request $request): JsonResponse
    {
        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');

        $this->manager->persist($habitat);
        $this->manager->flush();
        
        $responseData = $this->serializer->serialize($habitat,'json');
        
        $location = $this->urlGenerator->generate(
            'app_api_arcadia_habitat_show',
            ['id' => $habitat->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

    }


    #[Route('/{id}',name:'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $habitat = $this->repository->FindOneBy (['id'=> $id]);
        if ($habitat) {
            $responseData = $this->serializer->serialize($habitat, 'json');
            return new JsonResponse($responseData, Response ::HTTP_OK,[], true);
        }
        return new JsonResponse(NULL, Response ::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name:'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if ($habitat) {
            $habitat = $this->serializer->deserialize(
                $request->getContent(),
                    Habitat::class,
                    'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]
            );
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}',name:'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if ($habitat) {
            $this->manager->remove($habitat);
            $this->manager->flush();

            return new JsonResponse(NULL, Response ::HTTP_NO_CONTENT);
        }
        return new JsonResponse(NULL, Response ::HTTP_NOT_FOUND);
    }
}
