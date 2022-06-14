<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/v1/articles", name="api_v1_article_cget", methods={"GET"})
     */
    public function cget()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('App:Article')->findAll();
        $normalizedArticles = [];

        foreach ($articles as $article) {
            $normalizedArticle = $this->article($article)
            $normalizedArticles[] = $normalizedArticle;
        }

        return $this->json($normalizedArticles);
    }

    /**
     * @Route("/api/v1/articles/{id}", name="api_v1_article_get", methods={"GET"})
     */
    public function getOne(int $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository('App:Article')->find($id);

            $normalizedArticle = $this->article($article);
        } catch (\Exception $exception) {
            //
        }

        return $this->json($normalizedArticle);
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
