<?php

declare(strict_types=1);

namespace App\Author;

use App\Entity\Author;
use Doctrine\Persistence\ManagerRegistry;

class ArticlesCounter implements ArticlesCounterInterface
{
    public function countArticles(Author $author): int
    {
        return $author->getArticles()->count();
    }
}