<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class MultipleLikeFilter implements FilterInterface
{
    private array $columns;

    private array $options;

    private array $includeValues = [];

    private array $excludeValues = [];

    /**
     * @param string|array $columns
     * @param array $options
     */
    public function __construct($columns, array $options = [])
    {
        $this->columns = (!is_array($columns)) ? [$columns] : $columns;
        $this->options = array_merge(['operator' => 'LIKE', 'matchFromStart' => []], $options);
    }

    public function bindValues($values): self
    {
        $values = explode(' ', $values);
        $values = array_filter($values, static function ($value) {
            return $value !== null && $value !== false && $value !== '';
        });

        foreach ($values as $word) {
            if ($word[0] == '-' && $word !== '-') {
                $this->excludeValues[] = substr($word, 1);
            } else {
                $this->includeValues[] = $word;
            }
        }

        return $this;
    }

    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $expressionBuilder = $builder->expr();

        $andConditions = [];
        foreach ($this->includeValues as $value) {
            $orConditions = [];
            foreach ($this->columns as $column) {
                $orConditions[] = $expressionBuilder->comparison(
                    $column,
                    $this->options['operator'],
                    $expressionBuilder->literal($this->leftWildcardOperator($column) . $value . '%', \PDO::PARAM_STR)
                );
            }
            $andConditions[] = $expressionBuilder->or(...$orConditions);
        }

        foreach ($this->excludeValues as $value) {
            foreach ($this->columns as $column) {
                $andCondition = $builder->expr()->comparison(
                    'COALESCE(' . $column . ", '')",
                    'NOT ' . $this->options['operator'],
                    $expressionBuilder->literal($this->leftWildcardOperator($column) . $value . '%', \PDO::PARAM_STR)
                );
                $andConditions[] = $andCondition;
            }
        }

        $builder->andWhere($expressionBuilder->and(...$andConditions));

        return $builder;
    }

    private function leftWildcardOperator($column): string
    {
        return isset($this->options['matchFromStart'])
        && is_array($this->options['matchFromStart'])
        && in_array($column, $this->options['matchFromStart']) ? '' : '%';
    }
}
