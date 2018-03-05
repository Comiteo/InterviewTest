<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Normalizer\ArticleNormalizer;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;

class ApiController extends FOSRestController
{
    /**
     * @Get("/api/v1/articles", name="api_v1_article_cget")
     */
    public function cgetAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findAll();

        $articleNormalizer = new ArticleNormalizer();
        $normalizedArticles = $articleNormalizer->normalizeMany($articles);

        return new JsonResponse($normalizedArticles);
    }

    /**
     * @Get("/api/v1/articles/{id}", name="api_v1_article_get")
     */
    public function getAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $article = $em->getRepository('AppBundle:Article')->find($id);

        if($article === null){
            return ((new JsonResponse(['code' => 404, 'error' => 'Entité introuvable']))
                ->setStatusCode(Response::HTTP_NOT_FOUND));
        }

        $articleNormalizer = new ArticleNormalizer();

        $response = new JsonResponse($articleNormalizer->normalize($article));
        $response->headers->set('last-modified', $article->getUpdatedAt());
        return $response;
    }

    /**
     * @Post("/api/v1/articles", name="api_v1_article_action")
     */
    public function storeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $article = new Article();
        $article->setContent($request->get('content'));
        $article->setTitle($request->get('title'));
        $article->setCreatedAt(new \DateTime());
        $article->setUpdatedAt(new \DateTime());
        $article->setAuthor($request->get('author'));

        $em->persist($article);
        $em->flush();

        $articleNormalizer = new ArticleNormalizer();

        return new JsonResponse($articleNormalizer->normalize($article));
    }
}
