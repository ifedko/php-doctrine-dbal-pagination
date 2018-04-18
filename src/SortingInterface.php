<?php

namespace Ifedko\DoctrineDbalPagination;

use Doctrine\DBAL\Query\QueryBuilder;

interface SortingInterface
{
    /**
     * @param mixed $values
     */
    public function bindValues($values);

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $builder
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function apply(QueryBuilder $builder);
}
