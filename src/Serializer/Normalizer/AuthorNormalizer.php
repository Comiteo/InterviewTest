<?php

namespace App\Serializer\Normalizer;

use App\Entity\Author;

class AuthorNormalizer
{
        public function normalize(Author $author)
    {
        return [
            "name"      => $author->getName(),
            "bio"    => $author->getBio(),
            "created_at" => $author->getCreatedAt(),
            "updated_at" => $author->getUpdatedAt(),
        ];
    }
}