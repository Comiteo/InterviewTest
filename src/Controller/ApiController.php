<?php

namespace App\Controller;

use App\Entity\Article;
use App\Serializer\Normalizer\ArticleNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/v1/articles", name="api_v1_article_cget", methods={"GET"})
     */
    public function cget(ArticleNormalizer $normalizer, EntityManagerInterface $em): JsonResponse
    {
        $articles = $em->getRepository(Article::class)->findAll();

        return new JsonResponse($normalizer->normalize($articles));
    }

    /**
     * @Route("/api/v1/articles/{id}", name="api_v1_article_get", methods={"GET"})
     */
    public function getOne(EntityManagerInterface $em, ArticleNormalizer $normalizer, int $id): JsonResponse
    {
        $article = $em->getRepository(Article::class)->find($id);

        if(!$article) {
            throw $this->createNotFoundException('The product does not exist');
        }

        return new JsonResponse($normalizer->normalize([$article]));
    }

    /**
     * @Post("/api/v1/articles", name="api_v1_article_action")
     */
    public function createAction(Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $article = Article::create($request->request->all());

        $errors = $validator->validate($article);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString, 500);
        }

        return new Response('', 201);
    }
}
