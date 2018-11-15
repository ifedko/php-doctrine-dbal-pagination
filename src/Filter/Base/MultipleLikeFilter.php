<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class MultipleLikeFilter implements FilterInterface
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array of string
     */
    private $includeValues = [];

    /**
     * @var array of string
     */
    private $excludeValues = [];

    /**
     * @param string|array $columns
     * @param array $options
     */
    public function __construct($columns, $options=[])
    {
        $this->columns = (!is_array($columns)) ? [$columns] : $columns;
        $this->options = array_merge(['operator' => 'LIKE', 'matchFromStart' => []], $options);
    }

    /**
     * {@inheritDoc}
     */
    public function bindValues($values)
    {
        $values = explode(' ', $values);
        $values = array_filter($values);

        foreach ($values as $word) {
            if ($word[0] == '-') {
                $this->excludeValues[] = substr($word, 1);
            } else {
                $this->includeValues[] = $word;
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(QueryBuilder $builder)
    {
        $andConditions = [];
        foreach ($this->includeValues as $value) {
            $orConditions = [];
            foreach ($this->columns as $column) {
                $orConditions[] = $builder->expr()->comparison(
                    $column,
                    $this->options['operator'],
                    $builder->expr()->literal($this->leftWildcardOperator($column) . $value . '%', \PDO::PARAM_STR)
                );
            }
            $andConditions[] = $builder->expr()->orX()->addMultiple($orConditions);
        }

        foreach ($this->excludeValues as $value) {
            foreach ($this->columns as $column) {
                $andCondition = $builder->expr()->comparison(
                    'COALESCE(' . $column . ", '')",
                    'NOT ' . $this->options['operator'],
                    $builder->expr()->literal($this->leftWildcardOperator($column) . $value . '%', \PDO::PARAM_STR)
                );
                $andConditions[] = $andCondition;
            }
        }

        $builder->andWhere($builder->expr()->andX()->addMultiple($andConditions));

        return $builder;
    }

    private function leftWildcardOperator($column)
    {
        return isset($this->options['matchFromStart'])
            && is_array($this->options['matchFromStart'])
            && in_array($column, $this->options['matchFromStart']) ? '' : '%';
    }
}
