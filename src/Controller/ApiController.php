<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @Route("/api/v1/articles", name="api_v1_article_cget", methods={"GET"})
     */
    public function cget(): JsonResponse
    {
        return $this->json($this->registry->getRepository(Article::class)->findAll());
    }

    /**
     * @Route("/api/v1/articles/{id}", name="api_v1_article_get", methods={"GET"})
     * @ParamConverter("article", class="App:Article")
     */
    public function getOne(Article $article, Request $request): JsonResponse
    {
        $response = new JsonResponse();

        $authorUpdatedAt = $article->getAuthor()->getUpdatedAt();
        $articleUpdatedAt = $article->getUpdatedAt();

        $response
            ->setLastModified($articleUpdatedAt > $authorUpdatedAt ? $articleUpdatedAt : $authorUpdatedAt)
            ->setPublic()
        ;

        if ($response->isNotModified($request)) {
            return $response->setStatusCode(Response::HTTP_NOT_MODIFIED);
        }

        return $this->json($article);
    }

    /**
     * @Route("/api/v1/articles", name="api_v1_article_create", methods={"POST"})
     */
    public function createAction(Request $request): JsonResponse
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            return $this->json($article, Response::HTTP_CREATED);
        }

        throw new BadRequestHttpException('Your form was incomplete, please check your fields or contact our support');
    }
}
