<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

final class ReportDataResolver implements QueryCollectionResolverInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function __invoke( $collection, array $context): iterable
    {

        $query = $this->em->createQuery(
            'SELECT p.id, p.descriptions, p.qty, p.unit, p.costprice, p.sellprice FROM App\Entity\Product p ORDER BY p.id ASC');

        $products = $query->getResult();
        return $products;        
    }
}


// ================REQUEST===============
// query reportdata {
//   reportdataProducts {
//     id
//     descriptions
//     qty
//     unit
//     costprice
//     sellprice
//   }
// }