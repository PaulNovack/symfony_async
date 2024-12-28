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
    public function aFindAll()
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $this->execAsynch($queryBuilder);
    }
}
