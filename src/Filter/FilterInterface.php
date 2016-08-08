<?php

namespace Ifedko\DoctrineDbalPagination\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

interface FilterInterface
{
    /**
     * @param mixed $values
     * @return void
     */
    public function bindValues($values);

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $builder
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function apply(QueryBuilder $builder);
}
