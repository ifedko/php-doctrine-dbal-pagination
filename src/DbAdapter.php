<?php

namespace Ifedko\DoctrineDbalPagination;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class DbAdapter
{
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
