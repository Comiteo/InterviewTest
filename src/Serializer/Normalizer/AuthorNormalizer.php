<?php

namespace App\Serializer\Normalizer;

use App\Entity\Author;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AuthorNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        return [
            "bio" => $data->getBio(),
            "name" => $data->getName(),
            "created_at" => $data->getCreatedAt()->format('c'),
            "updated_at" => $data->getUpdatedAt()->format('c'),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\Author;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
