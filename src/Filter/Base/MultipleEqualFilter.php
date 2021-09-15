<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class MultipleEqualFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $column;

    /**
     * @var array
     */
    private $values;

    private $type;

    /**
     * @param string $column
     */
    public function __construct($column, $type = Connection::PARAM_STR_ARRAY)
    {
        $this->column = $column;
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValues($values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        $this->values = $values;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(QueryBuilder $builder)
    {
        $builder
            ->andWhere($this->column . " IN (" . $builder->createNamedParameter($this->values, $this->type) . ")");
        return $builder;
    }
}
