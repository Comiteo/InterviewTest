<?php

namespace App\Service;

use App\Entity\Author;

class AuthorService
{
    public function getAuthorArticleCount(Author $author)
    {
        return $author->getArticles()->count();
    }

}