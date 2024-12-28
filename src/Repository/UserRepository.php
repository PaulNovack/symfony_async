<?php

namespace App\Repository;

use App\Repository\CustomRepository;

class UserRepository extends CustomRepository
{
    public function searchByName(string $searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.firstName LIKE :searchTerm OR u.lastName LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');

        $query = $queryBuilder->getQuery();
        $this->aSql = $query->getSQL();

        // Replace placeholders with actual parameter values
        $params = $query->getParameters();
        foreach ($params as $param) {
            $this->aSql = str_replace('?', $param->getValue(), $this->aSql);
        }
        die($this->aSql);

    }
}
