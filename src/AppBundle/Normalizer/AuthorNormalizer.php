<?php

namespace AppBundle\Normalizer;


class AuthorNormalizer
{
    public function normalize($author)
    {
        return [
            "name"      => $author->getName(),
            "bio"    => $author->getBio(),
            "created_at" => $author->getCreatedAt()->format('c'),
            "updated_at" => $author->getUpdatedAt()->format('c'),
        ];
    }
}