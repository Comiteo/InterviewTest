<?php

namespace App\Serializer\Normalizer;

use App\Entity\Article;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleNormalizer
{
    private $router;

    private $authorNormalizer;

    public function __construct(UrlGeneratorInterface $router, AuthorNormalizer $authorNormalizer)
    {
        $this->router = $router;
        $this->authorNormalizer = $authorNormalizer;
    }

    public function normalize($articles): array
    {
        $normalizedArticles = [];

        foreach ($articles as $article) {
            /** @var Article $article */
            $normalizedArticles[] = [
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'author' => $article->getAuthor() ? $this->authorNormalizer->normalize($article->getAuthor()): '',
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