<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use ZMQ;
use ZMQSocket;
use ZMQContext;

class CustomRepository extends EntityRepository
{
    // Add your custom methods here
    private $aSql;
    private $clientId;
    private $queryId;
    private $context;
    private $socket;

    public function __construct(EntityManagerInterface $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
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
    public function aSyncGet($query)
    {
        $this->queryId = uniqid("query_");
        $payload = msgpack_pack(['id' => $this->queryId, 'query' => $query]);
        $this->socket->sendmulti(['', $payload]);
    }

    public function aFetch()
    {
        $response = $this->socket->recvMulti();
        $payload = msgpack_unpack($response[0]);

        if (isset($payload['id']) && $payload['id'] === $this->queryId) {
            $receivedData = $payload['data'];
            echo "<br><br>";
            print_r($receivedData);
            return $receivedData;
        }

        return null;
    }

    public function findAsync()
    {
        // Custom logic for asynchronous fetching
        return $this->findAll();
    }
}
