<?php

namespace App\Resolver;

use Symfony\Component\Console\Output\ConsoleOutput;
use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Entity\Sale;
use Doctrine\ORM\EntityManagerInterface;

final class SalesDataResolver implements QueryCollectionResolverInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    /**
     * @param iterable<Sale> $collection
     * @return iterable<Sale>
     */
    public function __invoke(iterable $collection, array $context): iterable
    {
        // $output = new ConsoleOutput();
        $query = $this->em->createQuery(
            'SELECT s.id, s.saleamount, s.saledate FROM App\Entity\Sale s ORDER BY s.saledate ASC');

        $sales = $query->getResult();

        // $output = new ConsoleOutput();
        return $sales;        
    }
}

// ===============REQUEST==============
// query saledata {
//   saledataSales {
//     id
//     saleamount
//     saledate
//   }
// }