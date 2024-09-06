<?php
// src/Controller/RedisController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Predis\Client;

class RedisController extends AbstractController
{
    public function index(): Response
    {
        $redis = new Client(); // Connexion à Redis

        // Stockage d'une valeur
        $redis->set('test_key', 'Hello Redis!');

        // Récupération de la valeur
        $value = $redis->get('test_key');

        return new Response('La valeur stockée dans Redis est : ' . $value);
    }
}
