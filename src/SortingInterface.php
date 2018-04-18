<?php

namespace Ifedko\DoctrineDbalPagination;

use Doctrine\DBAL\Query\QueryBuilder;

interface SortingInterface
{
    /**
     * @param array $values
     * @return array values that were actually used to define sorting
     */
    public function bindValues($values);

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $builder
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function apply(QueryBuilder $builder);
}
