<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/v1/articles", name="api_v1_article_cget", methods={"GET"})
     */
    public function cget()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->json($em->getRepository('App:Article')->findAll());
    }

    /**
     * @Route("/api/v1/articles/{id}", name="api_v1_article_get", methods={"GET"})
     */
    public function getOne(int $id)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->json($article = $em->getRepository('App:Article')->find($id));
    }
}
