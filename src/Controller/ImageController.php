<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
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



#[Route('api/image', name:'app_api_arcadia_image_')]


class ImageController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $manager,
        public ImageRepository $repository,
        public SerializerInterface $serializer,
        public UrlGeneratorInterface $urlGenerator,
    ){
    }

    #[Route('api/image', name:'app_api_arcadia_image_', methods:'POST')]
    public function uploadImage(Request $request, EntityManagerInterface $em): Response
    {
        $file = $request->files->get('image');
        
        if ($file) {
            $image = new Image();
            $imageData = file_get_contents($file->getPathname());
            $image->setImageData($imageData);

            $em->persist($image);
            
        
$em->flush();

            return new Response('Image uploaded successfully', Response::HTTP_CREATED);
        }

        return new Response('No image uploaded', Response::HTTP_BAD_REQUEST);
    }


    #[Route('/get/{id}', name: 'image_view', methods: 'GET')]
    public function getImage($id, EntityManagerInterface $entityManager)
    {
        $image = $entityManager->getRepository(Image::class)->find($id);
    
        if (!$image) {
            return new JsonResponse(['error' => 'Image not found'], 404);
        }
    
        $imageData = stream_get_contents($image->getImageData());
        $imageBase64 = base64_encode($imageData);
    
        $data = [
            'id' => $image->getId(),
            'image_data' => $imageBase64,
        ];
    
        return new JsonResponse($data);
    }

    #[Route('/image/{id}', name: 'image_delete', methods: ['DELETE'])]
    public function deleteImage(ImageRepository $imageRepository, EntityManagerInterface $em, int $id): Response
    {
        $image = $imageRepository->find($id);

        if (!$image) {
            return new Response('Image not found', Response::HTTP_NOT_FOUND);
        }

        $em->remove($image);
        $em->flush();

        return new Response('Image deleted successfully', Response::HTTP_OK);
    }
}
