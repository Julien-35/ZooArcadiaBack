<?php

namespace App\Controller;

use App\Entity\Horaire;
use App\Repository\HoraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/horaires', name: 'app_api_arcadia_horaire_')]
class HoraireController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HoraireRepository $repository,
        private SerializerInterface $serializer
    ) {}

    #[Route('/get', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $horaires = $this->repository->findAll();

        if (!$horaires) {
            return new JsonResponse(['error' => 'No horaires found'], Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($horaires, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $horaire = $this->repository->find($id);

        if (!$horaire) {
            return new JsonResponse(['error' => 'No horaire found for id ' . $id], Response::HTTP_NOT_FOUND);
        }

        $responseData = $this->serializer->serialize($horaire, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $horaire = $this->repository->find($id);
    
        if (!$horaire) {
            return new JsonResponse(['error' => 'No horaire found for id ' . $id], Response::HTTP_NOT_FOUND);
        }
    
        $data = json_decode($request->getContent(), true);
    
        // Assurez-vous que les noms de champs correspondent à ceux attendus
        $titre = $data['titre'] ?? $horaire->getTitre();
        $message = $data['message'] ?? $horaire->getMessage();
        $heureDebut = $data['heureDebut'] ?? $horaire->getHeureDebut();
        $heureFin = $data['heureFin'] ?? $horaire->getHeureFin();
        $jour = $data['jour'] ?? $horaire->getJour();
    
        $horaire->setTitre($titre);
        $horaire->setMessage($message);
        $horaire->setHeureDebut($heureDebut);
        $horaire->setHeureFin($heureFin);
        $horaire->setJour($jour);
    
        $this->manager->flush();
    
        $responseData = $this->serializer->serialize($horaire, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
}
