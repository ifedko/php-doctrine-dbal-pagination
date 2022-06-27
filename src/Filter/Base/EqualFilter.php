<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class EqualFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $column;

    /**
     * @var int
     */
    private $value;

    /**
     * @var int
     */
    private $type;

    /**
     * @param string $column
     * @param int $type \PDO::PARAM_* constant
     */
    public function __construct(string $column, int $type)
    {
        $this->column = $column;
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValues($values): void
    {
        $this->value = ($this->type === \PDO::PARAM_INT) ? (int) $values : $values;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $builder->andWhere($builder->expr()->eq($this->column, $builder->expr()->literal($this->value, $this->type)));

        return $builder;
    }
}
