<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use ZMQContext;
use ZMQ;
use ZMQSocket;

class CustomRepository extends EntityRepository
{
    // Add your custom methods here
    private $aSql;
    private $clientId;
    private $queryId;
    private $context;
    private $socket;

    public function __construct()
    {
        $this->context = new ZMQContext();
        $this->clientId = uniqid("client_");
        $this->socket = $this->context->getSocket(ZMQ::SOCKET_REQ);
        $this->socket->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $this->clientId);
        $this->socket->connect("tcp://cpp_server:5555");
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
