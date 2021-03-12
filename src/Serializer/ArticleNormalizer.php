<?php 

namespace App\Serializer;

use App\Entity\Article;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ArticleNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($article, $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($article, $format, $context);
        $data['createdAt'] = $article->getCreatedAt()->format('c');
        $data['updatedAt'] = $article->getUpdatedAt()->format('c');
        $data['uri']= $this->router->generate('api_v1_article_get', [
            'id' => $article->getId(),
        ]);

        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Article;
    }
}