<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Habitat; 
use App\Entity\Race; 
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
use Predis\Client;



#[Route('api/animal', name:'app_api_arcadia_animal_')]
class AnimalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AnimalRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){}

    #[Route('/post', name: 'app_api_arcadia_animal_post', methods: ['POST'])]
    #[OA\Post(
        path: "/api/animal/post",
        summary: "Créer un nouvel animal",
        requestBody: new RequestBody(
            required: true,
            description: "Pour créer un nouvel animal, suivez les informations ci-dessous",
            content: new MediaType(
                mediaType: "application/json",
                schema: new Schema(
                    type: "object",
                    properties: [
                        new Property(property: "prenom", type: "string", example: "Dromdrom"),
                        new Property(property: "etat", type: "string", example: "Mal au ventre"),
                        new Property(property: "nourriture", type: "string", example: "Sable"),
                        new Property(property: "grammage", type: "string", example: "5kg"),
                        new Property(property: "habitat_id", type: "integer", example: 1),
                        new Property(property: "race_id", type: "integer", example: 1),
                        new Property(property: "image_data", type: "string", example: "base64encodedstring"),
                        new Property(property: "created_at", type: "string", example: "2024-07-08"),
                        new Property(property: "feeding_time", type: "string", example: "19:52:00")
                    ]
                )
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Création de l'animal",
        content: new OA\JsonContent(type: 'string')
    )]
    public function new(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
    
            $animal = new Animal();
            $animal->setPrenom($data['prenom']);
            $animal->setEtat($data['etat']);
            $animal->setNourriture($data['nourriture']);
            $animal->setGrammage($data['grammage']);
    
            $habitat = $this->manager->getRepository(Habitat::class)->find($data['habitat_id']);
            if (!$habitat) {
                return new JsonResponse(['error' => 'Habitat non trouvé'], Response::HTTP_NOT_FOUND);
            }
            $animal->setHabitat($habitat);
    
            $race = $this->manager->getRepository(Race::class)->find($data['race_id']);
            if (!$race) {
                return new JsonResponse(['error' => 'Race non trouvée'], Response::HTTP_NOT_FOUND);
            }
            $animal->setRace($race);
    
            $animal->setImageData($data['image_data']);
    
            $animal->setCreatedAt(isset($data['created_at']) && $this->validateDate($data['created_at'], 'Y-m-d') ? new \DateTime($data['created_at']) : new \DateTime());
            if (isset($data['feeding_time']) && $this->validateDate($data['feeding_time'], 'H:i:s')) {
                $animal->setFeedingTime(new \DateTime($data['feeding_time']));
            }
    
            $this->manager->persist($animal);
            $this->manager->flush();
    
            return new JsonResponse(['message' => 'Animal créé avec succès'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    
    #[Route('/get', name: 'show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse 
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('a', 'h', 'r')
            ->from(Animal::class, 'a')
            ->leftJoin('a.habitat', 'h')
            ->leftJoin('a.race', 'r');
    
        $query = $queryBuilder->getQuery();
        $animals = $query->getArrayResult();
    
        $responseData = $serializer->serialize($animals, 'json');
    
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/prenoms', name: 'get_all_prenoms', methods: ['GET'])]
    public function getAllPrenoms(): JsonResponse
    {
        $animals = $this->animalRepository->findAll();
        $prenoms = array_map(fn($animal) => $animal->getPrenom(), $animals);

        return new JsonResponse($prenoms);
    }


    #[Route('/api/habitat', name: 'app_api_arcadia_animal_get_habitats', methods: ['GET'])]
    public function getHabitats(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $habitats = $entityManager->getRepository(Habitat::class)->findAll();
        $responseData = $serializer->serialize($habitats, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/race', name: 'app_api_arcadia_animal_get_races', methods: ['GET'])]
    public function getRaces(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $races = $entityManager->getRepository(Race::class)->findAll();
        $responseData = $serializer->serialize($races, 'json');
        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]

    public function updateAnimal(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $animal = $entityManager->getRepository(Animal::class)->find($id);
    
        if (!$animal) {
            throw $this->createNotFoundException('Animal not found');
        }
    
        $data = json_decode($request->getContent(), true);
    
        if (isset($data['image_data'])) {
            $animal->setImageData($data['image_data']);
        }
    
        if (isset($data['prenom'])) {
            $animal->setPrenom($data['prenom']);
        }
        if (isset($data['etat'])) {
            $animal->setEtat($data['etat']);
        }
        if (isset($data['nourriture'])) {
            $animal->setNourriture($data['nourriture']);
        }
        if (isset($data['grammage'])) {
            $animal->setGrammage($data['grammage']);
        }
        if (isset($data['created_at'])) {
            $animal->setCreatedAt(new \DateTime($data['created_at']));
        }
        if (isset($data['feeding_time'])) {
            $animal->setFeedingTime(new \DateTime($data['feeding_time']));
        }
    
        $entityManager->persist($animal);
        $entityManager->flush();
    
        return new Response('Animal updated successfully');
    }
    
    
    
    #[Route('/{id}', name: 'app_api_animal_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $animal = $this->manager->getRepository(Animal::class)->find($id);
            if (!$animal) {
                return new JsonResponse(['error' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
            }
    
            $this->manager->remove($animal);
            $this->manager->flush();
    
            return new JsonResponse(['message' => 'Animal supprimé avec succès'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    


    #[Route('/increment/{animal}', name: 'app_api_increment_animal', methods: ['POST'])]
    public function increment(string $animal): JsonResponse
    {
        $key = 'animal:' . $animal . ':count';
        $count = $this->redisClient->incr($key);

        return new JsonResponse(['count' => $count]);
    }

    #[Route('/get-count/{animal}', name: 'app_api_get_animal_count', methods: ['GET'])]
    public function getCount(string $animal): JsonResponse
    {
        $key = 'animal:' . $animal . ':count';
        $count = $this->redisClient->get($key);

        return new JsonResponse(['count' => $count ?: 0]); 
    }
}


