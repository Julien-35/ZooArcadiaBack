<?php

namespace App\Serializer\Normalizer;

use App\Entity\Animal;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Serializer\Normalizer\AnimalNormalizer;

class AnimalNormalizer implements ContextAwareNormalizerInterface
{
    private ObjectNormalizer $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Animal;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($object, $format, $context);
    
        // Déboguer les données avant la modification
        dump($data); // Vérifiez les données avant modification
        die(); // Stopper l'exécution pour voir le dump
    
        // Manipuler le champ imageDataString
        if (isset($data['imageDataString']) && is_string($data['imageDataString'])) {
            $data['imageDataString'] = null; // Ou toute autre transformation nécessaire
        }
    
        return $data;
    }
}
