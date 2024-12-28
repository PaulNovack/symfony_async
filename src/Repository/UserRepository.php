<?php

namespace App\Repository;

use App\Repository\CustomRepository;

class UserRepository extends CustomRepository
{
    public function searchByName(string $searchTerm)
    {
        $this->aSql = $this->createQueryBuilder('u')
            ->where('u.firstName LIKE :searchTerm OR u.lastName LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')->getQuery()->getSQL();

    }
}
