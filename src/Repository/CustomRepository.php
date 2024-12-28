<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CustomRepository extends EntityRepository
{
    // Add your custom methods here
    private $lastExecutedSql;

    public function getLastExecutedSql(): ?string
    {
        return $this->lastExecutedSql;
    }

    public function storeSqlForQuery($queryBuilder)
    {
        $this->lastExecutedSql = $queryBuilder->getQuery()->getSQL();
    }
    public function findAsync()
    {
        // Custom logic for asynchronous fetching
        return $this->findAll();
    }
    public function searchByName(string $searchTerm)
    {
        return $this->createQueryBuilder('u')
            ->where('u.firstName LIKE :searchTerm OR u.lastName LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    }
}
