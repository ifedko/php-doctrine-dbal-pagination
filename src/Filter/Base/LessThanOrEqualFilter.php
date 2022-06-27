<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;


use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class LessThanOrEqualFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $column;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $column
     */
    public function __construct(string $column)
    {
        $this->column = $column;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValues($values): void
    {
        $this->value = $values;
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $builder->andWhere($builder->expr()->lte($this->column, $builder->expr()->literal($this->value)));

        return $builder;
    }
}
