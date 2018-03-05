<?php

namespace AppBundle\Normalizer;

class ArticleNormalizer
{
    public function normalizeMany($articles)
    {
        $normalizedArticles = [];
        foreach($articles as $article){
            $normalizedArticles[] = $this->normalize($article);
        }
        return $normalizedArticles;
    }

    public function normalize($article)
    {
        $authorNormalizer = new AuthorNormalizer();
        return [
            "title"      => $article->getTitle(),
            "content"    => $article->getContent(),
            "author"     => $authorNormalizer->normalize($article->getAuthor()),
            "created_at" => $article->getCreatedAt()->format('c'),
            "updated_at" => $article->getUpdatedAt()->format('c'),
        ];
    }
}