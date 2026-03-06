<?php

namespace App\Resolver;

use Symfony\Component\Console\Output\ConsoleOutput;
use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\Pagination;
use App\Entity\Product;
use App\Repository\ProductRepository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

final class GetProductSearchResolver implements QueryCollectionResolverInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Pagination $pagination,
        private ProductRepository $repository

    ) {}

    /**
     * @param iterable<Product> $collection
     * @return iterable<Product>
     */
    public function __invoke(iterable $collection, array $context): iterable
    {

        $output = new ConsoleOutput();
        $page = $context['args']['page'];
        $key = $context['args']['keyword'];
        // $output->writeln("PAGE..................." . $id);
        
        $perPage = 5;
        $offset = ($page - 1) * $perPage;


        $search = '%' . strtolower($key) . '%';
        $queryBuilder = $this->em->getRepository(Product::class)->createQueryBuilder('p');        
         $queryBuilder->where($queryBuilder->expr()->like('LOWER(p.descriptions)', ':keyword'))
           ->setParameter('keyword', $search)
           ->orderBy('p.descriptions', 'ASC')
           ->setFirstResult($offset)
           ->setMaxResults($perPage);
        
        $doctrinePaginator = new DoctrinePaginator($queryBuilder);
        $totalItems = count($doctrinePaginator);
        return new TraversablePaginator(
            $doctrinePaginator->getIterator(),
            (float) $page,
            (float) $perPage,
            (float) $totalItems
        );        
    }
}

// ==========REQUEST===================
// query searchdata($page: Int!, $keyword: String!) {
//   searchdataProducts(page: $page, keyword: $keyword) {      
//     collection {
//       id
//     	category
//       descriptions
//       qty
//       unit
//       costprice
//       sellprice
//       saleprice
//       productpicture
//       alertstocks
//       criticalstocks
//     }
//     paginationInfo {
//       currentPage
//       itemsPerPage
//       lastPage
//       totalCount
//     }        
//   }
// }


// VARIABLES
// {
//   "page": 1,
//   "keyword": "ipad"
// }