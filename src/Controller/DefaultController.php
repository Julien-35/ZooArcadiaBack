<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return new Response('Hello world !!');
    }

    /**
     * @Route("/api/avis/get", name="avis_get", methods={"GET"})
     */
    public function getAvis(): Response
    {
        return new Response('get avis');
    }

    /**
     * @Route("/api/horaire/get", name="horaire_get", methods={"GET"})
     */
    public function getHoraire(): Response
    {
        return $this->json(['message' => 'get horaire']);
    }
}
