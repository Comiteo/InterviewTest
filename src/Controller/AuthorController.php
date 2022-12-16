<?php

namespace App\Controller;

use App\Author\ArticlesCounterInterface;
use App\Entity\Author;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    private ArticlesCounterInterface $articlesCounter;

    public function __construct(ArticlesCounterInterface $articlesCounter) {
        $this->articlesCounter = $articlesCounter;
    }

    /**
     * @Route("/api/v1/authors/{id}/counter", name="api_v1_authors_counter", methods={"GET"})
     * @ParamConverter("author", class="App:Author")
     */
    public function counter(Author $author): JsonResponse
    {
        $response = $this->json([
            'counter' => $this->articlesCounter->countArticles($author)
        ]);

        $response
            ->setTtl(PHP_INT_MAX)
        ;

        return $response;
    }
}
