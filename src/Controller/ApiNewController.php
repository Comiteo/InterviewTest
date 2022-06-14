<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiNewController extends AbstractController
{
    /**
     * @Route("/api/v1/articles", name="api_v1_article_post", methods={"POST"})
     */
    public function post($request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $normalizedArticle = $this->article($article);

            return $this->json($normalizedArticle);
        }

        return $this->json(null, 404);
    }

    function article($article) {
        $normalizedArticle = [
            "title" => $article->getTitle(),
            "content" => $article->getContent(),
            "author" => $article->getAuthor(),
            "created_at" => $article->getCreatedAt()->format('c'),
            "updated_at" => $article->getUpdatedAt()->format('c'),
            "uri" => $this->generateUrl('api_v1_article_get', ["id" => $article->getId()]),
        ];

        return $normalizedArticle;
    }
}
