<?php

namespace App\Repository;

use App\Repository\CustomRepository;

class ProductRepository extends CustomRepository
{
    public function aFindAll()
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $this->execAsynch($queryBuilder);
    }
}
