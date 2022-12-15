<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ArticleNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;
    private $router;

    public function __construct(ObjectNormalizer $normalizer, RouterInterface $router)
    {
        $this->normalizer = $normalizer;
        $this->router = $router;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        return [
            "title" => $data->getTitle(),
            "content" => $data->getContent(),
            "author" => $data->getAuthor(),
            "created_at" => $data->getCreatedAt()->format('c'),
            "updated_at" => $data->getUpdatedAt()->format('c'),
            "uri" => $this->router->generate('api_v1_article_get', ["id" => $data->getId()]),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof \App\Entity\Article;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
