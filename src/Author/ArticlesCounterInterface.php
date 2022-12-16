<?php

declare(strict_types=1);

namespace App\Author;

use App\Entity\Author;

interface ArticlesCounterInterface
{
    public function countArticles(Author $author): int;
}