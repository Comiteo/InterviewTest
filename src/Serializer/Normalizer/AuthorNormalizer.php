<?php

namespace App\Serializer\Normalizer;

use App\Author\ArticlesCounterInterface;
use App\Entity\Author;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AuthorNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;
    private ArticlesCounterInterface $articlesCounter;

    public function __construct(ObjectNormalizer $normalizer, ArticlesCounterInterface $articlesCounter)
    {
        $this->normalizer = $normalizer;
        $this->articlesCounter = $articlesCounter;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        return [
            "bio" => $data->getBio(),
            "name" => $data->getName(),
            "created_at" => $data->getCreatedAt()->format('c'),
            "updated_at" => $data->getUpdatedAt()->format('c'),
            'nb_articles' => $this->articlesCounter->countArticles($data),
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
