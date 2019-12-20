<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findOnlyPublishedWithPaging(int $currentPage, int $nbPerPage)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.published = true')
            ->orderBy('a.createdAt', 'DESC')
            ->leftJoin('a.comments','c')
            ->addSelect('c')
            ->addOrderBy('c.createdAt', 'DESC')
            ->leftJoin('a.categories', 'cat')
            ->addSelect('cat')
            ->setFirstResult(($currentPage - 1) * $nbPerPage)
            ->setMaxResults($nbPerPage);
        return new Paginator($query);
    }

    public function findOnlyPublishedByCategory(Category $category)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.published = true')
            ->andWhere('cat =  :category')
            ->orderBy('a.createdAt', 'DESC')
            ->leftJoin('a.comments','c')
            ->addSelect('c')
            ->addOrderBy('c.createdAt', 'DESC')
            ->leftJoin('a.categories', 'cat')
            ->leftJoin('a.categories', 'cate')
            ->addSelect('cate')
            ->setParameter(':category', $category);

        return $query->getQuery()->getResult();
    }

    public function findOnlyPublishedByCategoryWithPading(Category $category, int $currentPage, int $nbPerPage)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.published = true')
            ->andWhere('cat = :category')
            ->orderBy('a.createdAt', 'DESC')
            ->leftJoin('a.categories', 'cat')
            ->leftJoin('a.categories', 'categories')
            ->leftJoin('a.comments', 'com')
            ->addSelect('categories')
            ->addSelect('com')
            ->setParameter(':category', $category)
            ->setFirstResult(($currentPage - 1) * $nbPerPage)
            ->setMaxResults($nbPerPage);
        ;

        return new Paginator($query);
    }


        // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
