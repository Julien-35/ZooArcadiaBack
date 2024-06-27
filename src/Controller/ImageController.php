<?php

namespace App\Controller;


use App\Entity\Image;
use DateTimeImmutable ;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\ImageType; 
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
        private EntityManagerInterface $manager,
        private ImageRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){

    }

    #[Route('/upload', name: 'post')]

    public function upload(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['filename']->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception if something happens during file upload
                }

                $image->setFilename($newFilename);
                $em->persist($image);
                $em->flush();

                return $this->redirectToRoute('image_upload');
            }
        }

        return $this->render('image/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/images", name="image_list")
     */
    public function list(EntityManagerInterface $em): Response
    {
        $images = $em->getRepository(Image::class)->findAll();

        return $this->render('image/list.html.twig', [
            'images' => $images,
        ]);
    }
}


//     #[Route('/get', name: 'show')]
//     public function show(): JsonResponse 
//     {
//         if (isset($_SERVER['HTTP_ORIGIN'])) {
//             // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
//             // you want to allow, and if so:
//             header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
//             header('Access-Control-Allow-Credentials: true');
//             header('Access-Control-Max-Age: 86400');    // cache for 1 day
//         }
         

//         // Access-Control headers are received during OPTIONS requests
//         if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
//             if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
//                 // may also be using PUT, PATCH, HEAD etc
//                 header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
            
//             if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
//                 header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
//             exit(0);
//         }
//         $image = $this->repository->findAll();
//         $responseData = $this->serializer->serialize($image, 'json');

//         return new JsonResponse($responseData, Response::HTTP_OK, [], true);
//     } 

//     #[Route('/{id}', name: 'edit')]
//     public function edit(int $id,Request $request): Response
//     {  if (isset($_SERVER['HTTP_ORIGIN'])) {
//         // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
//         // you want to allow, and if so:
//         header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
//         header('Access-Control-Allow-Credentials: true');
//         header('Access-Control-Max-Age: 86400');    // cache for 1 day
//     }
    
//     // Access-Control headers are received during OPTIONS requests
//     if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
//         if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
//             // may also be using PUT, PATCH, HEAD etc
//             header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        
//         if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
//             header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
//         exit(0);
//     }
//         $image = $this->repository->findOneBy(['id' => $id]);
//         if ($image) {
//             $image = $this->serializer->deserialize(
//                 $request->getContent(),
//                 Image::class,
//                     'json',
//                 [AbstractNormalizer::OBJECT_TO_POPULATE => $image]
//             );
//         $this->manager->flush();

//         return new JsonResponse(null, Response::HTTP_NO_CONTENT);
//     }
//     return new JsonResponse(null, Response::HTTP_NOT_FOUND);
//     }




// //     public function delete(int $id): Response
// //     {
// //         $image = $this->repository->findOneBy(['id' => $id]);
// //         if (!$image) {
// //             throw $this->createNotFoundException("Aucun image trouvé {$id} id");
// //         }
// //         $this->manager->remove($image);
// //         $this->manager->flush();
// //         return $this->json(['message' => "L'image a été supprimé."], Response::HTTP_NO_CONTENT);
// //     }
// }

