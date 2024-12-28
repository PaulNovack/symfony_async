<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use ZMQContext;

class CustomRepository extends EntityRepository
{
    // Add your custom methods here
    private $aSql;
    private $clientId;
    private $queryId;
    private $context;

    public function __construct()
    {
        $this->context = new ZMQContext();
    }

    public function getASql(): ?string
    {
        return $this->aSql;
    }

    public function storeSqlForQuery($queryBuilder)
    {
        $this->aSql = $queryBuilder->getQuery()->getSQL();
    }
    public function fetchResults($queryBuilder)
    {
        return $queryBuilder->getQuery()->getResult();
    }

    public function findAsync()
    {
        // Custom logic for asynchronous fetching
        return $this->findAll();
    }
}
