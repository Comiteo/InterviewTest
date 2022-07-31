<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('App:Article')->findAll();

        return $this->render(
            'article/index.html.twig',
            [
                'articles' => $articles,
            ]
        );
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $this->cache->delete("author_articles_count_{$article->getAuthor()->getId()}");

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/new.html.twig',
            [
                'article' => $article,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->find($id);

        return $this->render(
            'article/show.html.twig',
            [
                'article' => $article,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->find($id);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/edit.html.twig',
            [
                'article' => $article,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->find($id);

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $author = $em->getRepository('App:Author')->find($article->getAuthor()->getId());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            if ($author) {
                $this->cache->delete("author_articles_count_{$author->getId()}");
            }
        }

        return $this->redirectToRoute('article_index');
    }
}
