<?php

namespace App\Repository;

use App\Repository\CustomRepository;

class UserRepository extends CustomRepository
{
    public function searchByName(string $searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.firstName LIKE :searchTerm OR u.lastName LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery();

        $this->storeSqlForQuery($queryBuilder);

        return null; // Or handle as needed
    }
}
