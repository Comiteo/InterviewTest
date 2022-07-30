<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Author;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

class ApiController extends AbstractController
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;
    private CacheInterface $cache;

    /**
     * @param NormalizerInterface $normalizer
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager, CacheInterface $cache)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    /**
     * @Route("/api/v1/articles", name="api_v1_article_get_list", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getArticles(): JsonResponse
    {
        return $this->json($this->entityManager->getRepository(Article::class)->findAll());
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

        return $this->json($article);
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

        return $this->json($article, 201);
    }

    /**
     * @Route("/api/v1/authors/{id}/countArticles", name="api_v1_author_articles_count", methods={"GET"})
     *
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function getArticlesNumberByAuthorId(int $id): JsonResponse
    {
        $authorRepository = $this->entityManager->getRepository(Author::class);

        $author = $authorRepository->find($id);

        if (! $author) {
            return $this->json(['404' => 'The author does not exist.'], 404);
        }

        $count = $this->cache->get("author_articles_count_{$id}", fn () => $authorRepository->getArticlesCount($id));

        return $this->json(['count' => $count]);
    }
}
