<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class FullWordMatchFilter implements FilterInterface
{
    private string $column;
    private string $value;

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    public function bindValues($values): self
    {
        $this->value = trim($values);

        return $this;
    }

    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $builder->orWhere($builder->expr()->eq($this->column, $builder->expr()->literal($this->value, \PDO::PARAM_STR)));

        return $builder;
    }
}
