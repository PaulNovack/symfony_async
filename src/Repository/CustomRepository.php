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
    public $aSql;
    private $clientId;
    private $queryId;
    private $context;
    private $socket;

    public function __construct(EntityManagerInterface $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->context = new ZMQContext();
        $this->clientId = uniqid("client_");
        $this->socket = $this->context->getSocket(ZMQ::SOCKET_DEALER);
        $this->socket->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $this->clientId);
        $this->socket->connect("tcp://127.0.0.1:5555");
    }

    public function getASql(): ?string
    {
        return $this->aSql;
    }

    public function storeSqlForQuery($queryBuilder)
    {
        $query = $queryBuilder->getQuery();
        $this->aSql = $query->getSQL();

        // Replace placeholders with actual parameter values
        $params = $query->getParameters();
        foreach ($params as $param) {
            $this->aSql = str_replace('?', "'" . $param->getValue() . "'", $this->aSql);
        }
    }

    public function aSyncGet()
    {
        $this->queryId = uniqid("query_");
        $payload = msgpack_pack(['id' => $this->queryId, 'query' => $this->aSql]);
        $this->socket->sendmulti(['', $payload]);
    }

    public function aSyncFetch() : array
    {
        $response = $this->socket->recvMulti();
        $payload = msgpack_unpack($response[0]);
        $entities = [];

        if (isset($payload['id']) && $payload['id'] === $this->queryId) {
            $receivedData = $payload['data'];
            $entityName = $this->getClassName();

            foreach ($receivedData as $data) {
                $entity = new $entityName();
                $reflectionClass = new \ReflectionClass($entity);

                foreach ($reflectionClass->getProperties() as $property) {
                    $propertyName = $property->getName();
                    $dataKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $propertyName)) . '_1'; // Adjust suffix to match data keys
                    if (isset($data[$dataKey])) {
                        $property->setAccessible(true);
                        $value = $data[$dataKey];
                        // Handle special cases like JSON fields
                        if ($propertyName === 'roles') {
                            $value = json_decode($value, true);
                        }
                        $property->setValue($entity, $value);
                    }
                }

                $entities[] = $entity;
            }
        }

        return $entities;
    }

    public function findAsync()
    {
        // Custom logic for asynchronous fetching
        return $this->findAll();
    }
}
