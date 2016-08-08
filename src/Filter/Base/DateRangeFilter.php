<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class DateRangeFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $column;

    /**
     * @var string
     */
    private $beginValue;

    /**
     * @var string
     */
    private $endValue;

    /**
     * @param string $column
     */
    public function __construct($column)
    {
        $this->column = $column;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValues($values)
    {
        $beginValue = !empty($values['begin']) ? $values['begin'] : null;
        $endValue = !empty($values['end']) ? $values['end'] : null;

        $this->beginValue = $beginValue;
        $this->endValue = $endValue;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(QueryBuilder $builder)
    {
        if (!$this->beginValue && !$this->endValue) {
            return $builder;
        }

        $andCondition = $builder->expr()->andX();
        if ($this->beginValue) {
            $startExpression = $builder->expr()->gte($this->column, $builder->expr()->literal($this->beginValue));
            $andCondition->add($startExpression);
        }

        if ($this->endValue) {
            $endExpression = $builder->expr()->lte($this->column, $builder->expr()->literal($this->endValue));
            $andCondition->add($endExpression);
        }

        $builder->andWhere($andCondition);
        return $builder;
    }
}
