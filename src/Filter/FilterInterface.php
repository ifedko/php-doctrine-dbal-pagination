<?php

namespace Ifedko\DoctrineDbalPagination\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

interface FilterInterface
{
    /**
     * @param mixed $values
     * @return $this
     */
    public function bindValues($values): self;

    public function apply(QueryBuilder $builder): QueryBuilder;
}
