<?php

namespace Ifedko\DoctrineDbalPagination;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class DbAdapter
{
    /**
     * @var Connection
     */
    private $dbConnection;

    /**
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->dbConnection;
    }

    public function matching(QueryBuilder $queryBuilder, $limit, $offset)
    {
        $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $sth = $queryBuilder->execute();
        return $sth->fetchAll();
    }

    public function matchingTotal(QueryBuilder $queryBuilder)
    {
        $sth = $queryBuilder->execute();
        return (int)$sth->rowCount();
    }
}
