<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;


#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer)
    {
    }

    #[Route('/registration', name: 'registration', methods: 'POST')]
    #[OA\Post(
        path:"/api/registration",
        summary:"Inscription d'un nouvel utilisateur",
        requestBody : new RequestBody(
            required: true,
            description : "Donnée de l'utilisateur à inscrire",
            content : [new Mediatype(mediaType: "application/json",
                schema : new Schema (type: "object", properties:[
                    new Property (
                        property: "email",
                        type : 'string',
                        example :'adresse@email.com'
                    ),
                    new Property (
                        property: "password",
                        type : "string",
                        example : "Votre mot de passe"
                    )
                ])
            )]
        ),
    )]
    #[OA\Response(
        response: 201,
        description:"Utilisateur inscrit avec succès",
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property (property:"email", type:"string", example:"Nom d'utilisateur"),
                new OA\Property (property:"password", type:"string", example:"azre31Z!"),
            ],
        )
    )]

    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse([
            'email'  => $user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles()
        ],
            Response::HTTP_CREATED
        );
    }


    #[Route('/account',name:'show', methods: 'GET')]

    
    public function show(): JsonResponse
    {
        
            $user = $this->getUser();
    
            $responseData = $this->serializer->serialize($user, 'json');
    
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }


#[Route('/login', name: 'login', methods: 'POST')]
#[OA\Post(
    path:"/api/login",
    summary:"Connexion d'un employé",
    requestBody : new RequestBody(required: true,
        description : "Donnée de l'utilisateur pour se connecter",
        content : [new Mediatype(mediaType: "application/json",
            schema : new Schema (type: "object", properties:[
                new Property (
                    property: "username",
                    type : 'string',
                    example :'adresse@email.com'
                ),
                new Property (
                    property: "password",
                    type : "string",
                    example : "Votre mot de passe"
                )
            ])
        )]
    )
)]


    public function login(#[CurrentUser] ?User $user): JsonResponse
    {

        if (null === $user) {
             return $this->json([
                 'message' => 'missing credentials',
             ], Response::HTTP_UNAUTHORIZED);

        }
        return $this->json([
            'username'  => $user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles()
        ]);
    }

}
