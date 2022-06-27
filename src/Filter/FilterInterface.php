<?php

namespace Ifedko\DoctrineDbalPagination\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

interface FilterInterface
{
    /**
     * @param mixed $values
     * @return void
     */
    public function bindValues($values): void;

    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder): QueryBuilder;
}
