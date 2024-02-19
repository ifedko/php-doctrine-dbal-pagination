<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class EqualFilter implements FilterInterface
{
    private string $column;

    /**
     * @var int|string
     */
    private $value;

    private int $type;

    public function __construct(string $column, int $type)
    {
        $this->column = $column;
        $this->type = $type;
    }

    /**
     * @param int|string $values
     * @return $this
     */
    public function bindValues($values): self
    {
        $this->value = ($this->type === \PDO::PARAM_INT) ? (int)$values : $values;

        return $this;
    }

    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $builder->andWhere($builder->expr()->eq($this->column, $builder->expr()->literal($this->value, $this->type)));

        return $builder;
    }
}
