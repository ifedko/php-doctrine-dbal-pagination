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
     * @var
     */
    private $type;

    /**
     * @param string $column
     * @param string $type \PDO::PARAM_* constant
     */
    public function __construct($column, $type)
    {
        $this->column = $column;
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValues($values)
    {
        $this->value = ($this->type === \PDO::PARAM_INT) ? intval($values) : $values;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(QueryBuilder $builder)
    {
        $builder->andWhere($builder->expr()->eq($this->column, $builder->expr()->literal($this->value, $this->type)));

        return $builder;
    }
}
