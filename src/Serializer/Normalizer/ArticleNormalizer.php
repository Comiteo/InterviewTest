<?php


namespace App\Serializer\Normalizer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleNormalizer
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function normalize($articles): array
    {
        $normalizedArticles = [];

        foreach ($articles as $article) {
            $normalizedArticles[] = [
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'author' => $article->getAuthor(),
                'created_at' => $article->getCreatedAt()->format('c'),
                'updated_at' => $article->getUpdatedAt()->format('c'),
                'uri' => $this->router->generate(
                    'api_v1_article_get',
                    ['id' => $article->getId()]
                ),
            ];
        }

        return $normalizedArticles;
    }
}