<?php

namespace App\Repository;

use App\Repository\CustomRepository;

class UserRepository extends CustomRepository
{
    public function searchByName(string $searchTerm)
    {
        $this->aSql = str_replace(
            ':searchTerm',
            $this->getEntityManager()->getConnection()->quote('%' . $searchTerm . '%'),
            $this->createQueryBuilder('u')
                ->where('u.firstName LIKE :searchTerm OR u.lastName LIKE :searchTerm')
                ->getQuery()
                ->getSQL()
        );

    }
}
