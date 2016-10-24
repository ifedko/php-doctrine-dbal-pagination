<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class LikeFilter implements FilterInterface
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var int
     */
    private $value;

    /**
     * @param string|array $columns
     */
    public function __construct($columns)
    {
        $this->columns = (!is_array($columns)) ? [$columns] : $columns;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValues($values)
    {
        $this->value = $values;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(QueryBuilder $builder)
    {
        $orConditions = [];
        foreach ($this->columns as $column) {
            $orCondition = $builder->expr()->like(
                $column,
                $builder->expr()->literal('%' . $this->value . '%', \PDO::PARAM_STR)
            );
            $orConditions[] = $orCondition;
        }
        $builder->andWhere($builder->expr()->orX()->addMultiple($orConditions));

        return $builder;
    }
}
