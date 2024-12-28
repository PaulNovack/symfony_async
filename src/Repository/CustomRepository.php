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

    public function __construct($em, $classMetadata)
    {
        parent::__construct($em, $classMetadata);
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
    public function aSyncGet()
    {
        $this->queryId = uniqid("query_");
        $payload = msgpack_pack(['id' => $this->queryId, 'query' => $this->aSql]);
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
        $this->aSyncGet();
        return $this->aFetch();
    }
}
