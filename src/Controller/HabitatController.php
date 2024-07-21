<?php

// src/Controller/HabitatController.php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/habitat', name: 'app_api_arcadia_habitat_')]
class HabitatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $repository,
        private SerializerInterface $serializer
    ) {}

    #[Route('/get', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $habitats = $this->repository->findAll();

        if (!$habitats) {
            return new JsonResponse(['error' => 'No habitats found'], Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($habitats, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $habitat = $this->repository->find($id);

        if (!$habitat) {
            return new JsonResponse(['error' => 'No habitat found for id ' . $id], Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($habitat, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->repository->find($id);

        if (!$habitat) {
            return new JsonResponse(['error' => 'No habitat found for id ' . $id], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $nom = $data['nom'] ?? $habitat->getNom();
        $description = $data['description'] ?? $habitat->getDescription();
        $commentaireHabitat = $data['commentaireHabitat'] ?? $habitat->getCommentaireHabitat();

        $habitat->setNom($nom);
        $habitat->setDescription($description);
        $habitat->setCommentaireHabitat($commentaireHabitat);

        $this->manager->flush();

        $responseData = $this->serializer->serialize($habitat, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
}
