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
        $this->options = array_merge(['operator' => 'LIKE'], $options);
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
        $orConditions = [];

        foreach ($this->columns as $column) {
            $andConditions = [];
            foreach ($this->includeValues as $value) {
                $andCondition = $builder->expr()->comparison(
                    $column,
                    $this->options['operator'],
                    $builder->expr()->literal('%' . $value . '%', \PDO::PARAM_STR)
                );
                $andConditions[] = $andCondition;
            }

            foreach ($this->excludeValues as $value) {
                $andCondition = $builder->expr()->comparison(
                    $column,
                    'NOT ' . $this->options['operator'],
                    $builder->expr()->literal('%' . $value . '%', \PDO::PARAM_STR)
                );
                $andConditions[] = $andCondition;
            }

            $orConditions[] = $builder->expr()->andX()->addMultiple($andConditions);
        }

        $builder->andWhere($builder->expr()->orX()->addMultiple($orConditions));

        return $builder;
    }
}
