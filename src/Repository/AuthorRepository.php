<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @param int $authorId
     *
     * @return integer
     */
    public function getArticlesCount(int $authorId): int
    {
        $articleRepository = $this->getEntityManager()->getRepository(Article::class);

        $count = $articleRepository->createQueryBuilder('article')
            ->select('count(article.id)')
            ->where("article.author = {$authorId}")
            ->getQuery()
            ->getScalarResult();

        return $count[0][1];
    }
}
