<?php

namespace App\Controller;


use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use App\Entity\Animal;
use App\Repository\AnimalRepository; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/rapportveterinaire', name: 'app_api_arcadia_rapportveterinaire_')]
class RapportVeterinaireController extends AbstractController
{
    private RapportVeterinaireRepository $repository;
    private AnimalRepository $animalRepository;
    private SerializerInterface $serializer;

    public function __construct(
        RapportVeterinaireRepository $repository,
        AnimalRepository $animalRepository,
        SerializerInterface $serializer
    ) {
        $this->repository = $repository;
        $this->animalRepository = $animalRepository;
        $this->serializer = $serializer;
    }

    #[Route('/post', name: 'app_api_arcadia_rapportveterinaire_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifier la présence des données
        if (!isset($data['date']) || !isset($data['detail']) || !isset($data['animal']['prenom'])) {
            return new JsonResponse(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        // Convertir la date en DateTime
        try {
            $date = new \DateTime($data['date']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Date invalide'], Response::HTTP_BAD_REQUEST);
        }

        $detail = $data['detail'];
        $animalPrenom = $data['animal']['prenom'];

        // Trouver l'animal par son prénom
        $animal = $this->animalRepository->findOneBy(['prenom' => $animalPrenom]);
        if (!$animal) {
            return new JsonResponse(['error' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Créer et sauvegarder le rapport vétérinaire
        $rapportveterinaire = new RapportVeterinaire();
        $rapportveterinaire->setDate($date);
        $rapportveterinaire->setDetail($detail);
        $rapportveterinaire->setAnimal($animal);

        $entityManager->persist($rapportveterinaire);
        $entityManager->flush();

        // Sérialiser la réponse
        $responseData = $this->serializer->serialize($rapportveterinaire, 'json', ['groups' => ['rapport_veterinaire']]);

        return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
    }


    #[Route('/get', name: 'show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse 
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('r', 'a')
            ->from(RapportVeterinaire::class, 'r')
            ->leftJoin('r.animal', 'a');
    
        $query = $queryBuilder->getQuery();
        $rapportVeterinaires = $query->getArrayResult();
    
        $responseData = $serializer->serialize($rapportVeterinaires, 'json');
    
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $rapportVeterinaires = $this->repository->findAll();
    
        if (empty($rapportVeterinaires)) {
            return new JsonResponse(['error' => 'No rapports found'], Response::HTTP_NOT_FOUND);
        }
    
        $responseData = $this->serializer->serialize($rapportVeterinaires, 'json');
     
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'app_api_arcadia_rapportveterinaire_edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $rapportveterinaire = $this->repository->find($id);

        if (!$rapportveterinaire) {
            return new JsonResponse(['message' => 'Rapport vétérinaire non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $updatedRapportVeterinaire = $this->serializer->deserialize(
            $request->getContent(),
            RapportVeterinaire::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $rapportveterinaire]
        );

        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
