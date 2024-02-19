<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class MultipleEqualFilter implements FilterInterface
{
    private string $column;

    private array $values;

    private int $type;

    public function __construct(string $column, $type = Connection::PARAM_STR_ARRAY)
    {
        $this->column = $column;
        $this->type = $type;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function bindValues($values): self
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        $this->values = $values;

        return $this;
    }

    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $builder
            ->andWhere($this->column . " IN (" . $builder->createNamedParameter($this->values, $this->type) . ")");

        return $builder;
    }
}
