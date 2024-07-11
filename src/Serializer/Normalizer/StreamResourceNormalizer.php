<?php

namespace App\Serializer\Normalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Entity\RapportVeterinaire;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class StreamResourceNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        if (is_resource($object) && get_resource_type($object) === 'stream') {
            return base64_encode(stream_get_contents($object));
        }

        return $object;
    }

    public function supportsNormalization($data, $format = null)
    {
        return is_resource($data) && get_resource_type($data) === 'stream';
    }
}


