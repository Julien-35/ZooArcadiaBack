<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use DateTimeImmutable ;
use App\Repository\RapportVeterinaireRepository;
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



#[Route('api/rapportveterinaire', name:'app_api_arcadia_rapportveterinaire_')]
class RapportVeterinaireController extends AbstractController
{

    public function getTodayDate(): JsonResponse
    {
        // Obtenir la date actuelle
        $today = new \DateTime();
        // Formater la date au format YYYY-MM-DD
        $formattedDate = $today->format('Y-m-d');

        // Retourner la date en réponse JSON
        return new JsonResponse(['date' => $formattedDate]);
    }



    public function __construct(
        private EntityManagerInterface $manager,
        private RapportVeterinaireRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){

    }

    #[Route(methods:'POST')]
    public function new(Request $request): JsonResponse
    {
        $rapportveterinaire = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json');

        $this->manager->persist($rapportveterinaire);
        $this->manager->flush();
        
        $responseData = $this->serializer->serialize($rapportveterinaire,'json');
        
        $location = $this->urlGenerator->generate(
            'app_api_arcadia_rapportveterinaire_show',
            [
           
             'date' => $rapportveterinaire->getDate(),
             'detail' => $rapportveterinaire->getDetail(),
        ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

    }


    #[Route('/get',name:'show')]
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

        $rapportveterinaire = $this->repository->findAll();
        $responseData = $this->serializer->serialize($rapportveterinaire, 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);


    } 



    #[Route('/{id}', name:'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $rapportveterinaire = $this->repository->findOneBy(['id' => $id]);
        if ($rapportveterinaire) {
            $rapportveterinaire = $this->serializer->deserialize(
                $request->getContent(),
                    RapportVeterinaire::class,
                    'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $rapportveterinaire]
            );
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}',name:'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $rapportveterinaire = $this->repository->findOneBy(['id' => $id]);
        if ($rapportveterinaire) {
            $this->manager->remove($rapportveterinaire);
            $this->manager->flush();

            return new JsonResponse(NULL, Response ::HTTP_NO_CONTENT);
        }
        return new JsonResponse(NULL, Response ::HTTP_NOT_FOUND);
    }
}
