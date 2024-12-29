<?php

namespace App\Repository;

use Doctrine\DBAL\Query\QueryException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use ZMQ;
use ZMQContext;

class CustomRepository extends EntityRepository
{
    private string $aSql;
    private $query; // parameterized query with ?
    private string $clientId;
    private string $queryId;
    private ZMQContext $context;
    private $socket;

    public function __construct(EntityManagerInterface $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->_em = $em;
        $this->_class = $class;
        $this->context = new ZMQContext();
        $this->clientId = uniqid("client_");
        $this->socket = $this->context->getSocket(ZMQ::SOCKET_DEALER);
        $this->socket->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $this->clientId);
        $this->socket->connect("tcp://127.0.0.1:5555");
    }

    public function execAsynch($queryBuilder): void
    {
        $this->query = $queryBuilder->getQuery();
        $this->aSql = $this->query->getSQL();

        // Replace placeholders with actual parameter values
        $params = $this->query->getParameters();
        foreach ($params as $param) {
            $this->aSql = str_replace('?', "'" . $param->getValue() . "'", $this->aSql);
        }
        $this->aSyncGet();
    }

    public function aSyncGet(): void
    {
        $this->queryId = uniqid("query_");
        $payload = msgpack_pack([
            'id' => $this->queryId,
            'query' => $this->aSql
        ]);
        $this->socket->sendmulti(['', $payload]);
    }

    public function aSyncFetch(): array
    {
        $response = $this->socket->recvMulti();
        $payload = msgpack_unpack($response[0]);
        $entities = [];

        if (isset($payload['id']) && $payload['id'] === $this->queryId) {
            if (isset($payload['ERROR:SQLException'])) {
                throw new QueryException(
                    "ZeroMQServiceMySQLConnectionException: "
                    . $payload['ERROR:SQLException']
                    . ' :: SQL: ' . $this->query->getSQL(),
                    1,
                    new \Exception("Database error in SQL execution: "
                    . $payload['ERROR:SQLException'] . ' in SQL Statement: ' . $this->query->getSQL())
                );
            }
            $receivedData = $payload['data'];
            $entityName = $this->getClassName();

            foreach ($receivedData as $data) {
                $entity = new $entityName();
                $reflectionClass = new \ReflectionClass($entity);

                foreach ($reflectionClass->getProperties() as $property) {
                    $propertyName = $property->getName();
                    $baseDataKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $propertyName));

                    foreach ($data as $key => $value) {
                        if (strpos($key, $baseDataKey) === 0) {
                            $property->setAccessible(true);

                            // Handle special cases like JSON fields
                            if ($propertyName === 'roles') {
                                $value = json_decode($value, true);
                            }
                            $property->setValue($entity, $value);
                            break;
                        }
                    }
                }
                $entities[] = $entity;
            }
        }

        return $entities;
    }
}
