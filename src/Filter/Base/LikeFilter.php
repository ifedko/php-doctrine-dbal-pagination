<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class LikeFilter implements FilterInterface
{
    private array $columns;

    private array $options;

    private string $value;

    /**
     * @param string|array $columns
     * @param ?array $options
     */
    public function __construct($columns, ?array $options = [])
    {
        $this->columns = (!is_array($columns)) ? [$columns] : $columns;
        $this->options = array_merge(['operator' => 'LIKE'], $options);
    }

    /**
     * @param string $values
     * @return $this
     */
    public function bindValues($values): self
    {
        $this->value = $values;
        return $this;
    }

    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $expressionBuilder = $builder->expr();

        $orConditions = [];
        foreach ($this->columns as $column) {
            $orCondition = $expressionBuilder->comparison(
                $column,
                $this->options['operator'],
                $expressionBuilder->literal('%' . $this->value . '%', \PDO::PARAM_STR)
            );
            $orConditions[] = $orCondition;
        }
        $builder->andWhere($expressionBuilder->or(...$orConditions));

        return $builder;
    }
}
