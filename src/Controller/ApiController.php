<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Author;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @Route("/api/v1/articles", name="api_v1_article_get_list", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getArticles(): JsonResponse
    {
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        return $this->json($this->normalizeArticles($articles));
    }

    /**
     * @Route("/api/v1/articles/{id}", name="api_v1_article_get", methods={"GET"})
     *
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function getArticleById(int $id) : JsonResponse
    {
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        if(!$article) {
            return $this->json(['404' => 'The article does not exist.'], 404);
        }

        return $this->json($this->normalizeArticle($article));
    }

    /**
     * @Route("/api/v1/articles", name="api_v1_article_post", methods={"POST"})
     * 
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createArticle(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $body = $request->getContent();

        $article = $this->serializer->deserialize($body, Article::class, 'json');

        $article->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        $errors = $validator->validate($article);

        if (count($errors) > 0) {
            return $this->json(['400' => $errors], 400);
        }

        $authorName = $article->getAuthor()->getName();

        if (!$authorName) {
            return $this->json(['400' => 'The author name must be provided.'], 400);
        }

        $author = $this->entityManager->getRepository(Author::class)->findByName($authorName);

        if (empty($author)) {
            return $this->json(['404' => 'The author does not exist.'], 404);
        }

        $article->setAuthor($author[0]);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json($this->normalizeArticle($article));
    }

    /**
     * @Route("/api/v1/author/{id}/countArticles", name="api_v1_author_articles_count", methods={"GET"})
     *
     * @param Request $request
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function getArticlesNumberByAuthor(Request $request, int $id): JsonResponse
    {
        $authorRepository = $this->entityManager->getRepository(Author::class);

        $author = $authorRepository->findById($id);

        if (empty($author)) {
            return $this->json(['404' => 'The author does not exist.'], 404);
        }

        $count = $this->entityManager->getRepository(Author::class)->getArticlesCount($id);

        return $this->json(['count' => $count]);
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
