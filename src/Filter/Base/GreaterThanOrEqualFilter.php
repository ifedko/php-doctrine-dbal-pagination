<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class GreaterThanOrEqualFilter implements FilterInterface
{
    private string $column;

    /**
     * @var int|string
     */
    private $value;

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    /**
     * @param int|string $values
     * @return $this
     */
    public function bindValues($values): self
    {
        $this->value = $values;
        return $this;
    }

    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $builder->andWhere($builder->expr()->gte($this->column, $builder->expr()->literal($this->value)));

        return $builder;
    }
}
