<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator; // You might use Pagerfanta's adapter instead
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findByPagination(int $limit, int $offset): array
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);        
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Product[] Returns an array of User objects
//     */
    public function findByPagination(int $limit, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('p')
        ->orderBy('p.createdAt', 'DESC')
        ->getQuery();
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        
        // 3. Set the first result (offset) and max results (limit) on the Paginator's internal query object
        $paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit); // This line is what was missing or misapplied

        return $paginator;        
    }


    public function findByLimitAndOffset(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit) // Sets the LIMIT
            ->setFirstResult($offset) // Sets the OFFSET
            ->getQuery()
            ->getResult();
    }


    public function createFindAllQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC');
    }

    public function searchByKeyword(string $keyword): array
    {
        $qb = $this->createQueryBuilder('p');

        // Use the LIKE operator in the where clause
        $qb->where($qb->expr()->like('p.descriptions', ':keyword'))
           ->setParameter('keyword', '%' . $keyword . '%')
           ->orderBy('p.id', 'ASC');

        return $qb->getQuery()->getResult();
    }


}

