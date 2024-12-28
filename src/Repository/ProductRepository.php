<?php

namespace App\Repository;

use App\Repository\CustomRepository;

class ProductRepository extends CustomRepository
{
    public function aSearchByName(string $searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');
        $this->execAsynch($queryBuilder);
    }
    public function aFindAll(int $limit = 25, int $offset = 0)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $this->execAsynch($queryBuilder);
    }
}
