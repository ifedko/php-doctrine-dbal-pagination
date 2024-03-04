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
        $expressionBuilder = $builder->expr();
        $builder->andWhere(
            $expressionBuilder->eq($this->column, $expressionBuilder->literal($this->value, $this->type))
        );

        return $builder;
    }
}
