<?php

namespace App\Repository;

use App\Repository\CustomRepository;

class UserRepository extends CustomRepository
{
    public function aSearchByName(string $searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.firstName LIKE :searchTerm OR u.lastName LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');
        $this->execAsynch($queryBuilder);
    }
    public function aFindAll(int $limit = 25, int $offset = 0)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $this->execAsynch($queryBuilder);
    }
}
