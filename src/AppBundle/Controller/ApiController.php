<?php

namespace AppBundle\Controller;

use AppBundle\Service\ArticleNormalizer;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends FOSRestController
{
    /**
     * @Get("/api/v1/articles", name="api_v1_article_cget")
     */
    public function cgetAction(ArticleNormalizer $normalizer)
    {
        $articles = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Article')
            ->findAll();

        return new JsonResponse($normalizer->normalize($articles));
    }

    /**
     * @Get("/api/v1/articles/{id}", name="api_v1_article_get")
     */
    public function getAction($id, ArticleNormalizer $normalizer)
    {
        $article = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Article')
            ->find($id);

        }

        return new JsonResponse($normalizer->normalize([$article])[0]);
    }

    /**
     */
    {



    }
}
