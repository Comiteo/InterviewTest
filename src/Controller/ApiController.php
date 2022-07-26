<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    private NormalizerInterface $normalizer;
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;

    /**
     * @param NormalizerInterface $normalizer
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(NormalizerInterface $normalizer, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->normalizer = $normalizer;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/v1/articles", name="api_v1_article_cget", methods={"GET"})
     */
    public function getArticles(): JsonResponse
    {
        $articles = $this->entityManager->getRepository('App:Article')->findAll();
        return $this->json($this->normalizeArticles($articles));
    }

    /**
     * @Route("/api/v1/articles/{id}", name="api_v1_article_get", methods={"GET"})
     */
    public function getArticleById(int $id) : JsonResponse
    {
        $article = $this->entityManager->getRepository('App:Article')->find($id);

        if(!$article) {
            return $this->json(['404' => 'The article does not exist.']);
        }

        return $this->json($this->normalizeArticle($article));
    }

    /**
     * @Route("/api/v1/articles", name="api_v1_article_post", methods={"POST"})
     */
    public function createArticle(Request $request): JsonResponse
    {
        $body = $request->getContent();

        $article = $this->serializer->deserialize($body, Article::class, 'json');

        $article->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json($this->normalizeArticle($article));
    }

    /**
     * @param Article $article
     *
     * @return array
     */
    private function normalizeArticle(Article $article): array
    {
        $normalizedArticle = $this->normalizer->normalize($article);
        $normalizedArticle['uri'] = $this->generateUrl('api_v1_article_get', ["id" => $article->getId()]);

        return $normalizedArticle;
    }

    /**
     * @param array<Article> $articles
     *
     * @return array
     */
    private function normalizeArticles(array $articles): array
    {
        foreach ($articles as $article) {
            $normalizedArticles[] = $this->normalizeArticle($article);
        }

        return $normalizedArticles ?? [];
    }
}
