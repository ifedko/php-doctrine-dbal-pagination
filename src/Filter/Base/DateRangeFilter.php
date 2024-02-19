<?php

namespace Ifedko\DoctrineDbalPagination\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

class DateRangeFilter implements FilterInterface
{
    private string $column;

    private ?string $beginValue;

    private ?string $endValue;

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function bindValues($values): self
    {
        $beginValue = !empty($values['begin']) ? $values['begin'] : null;
        $endValue = !empty($values['end']) ? $values['end'] : null;

        $this->beginValue = $beginValue;
        $this->endValue = $endValue;

        return $this;
    }

    public function apply(QueryBuilder $builder): QueryBuilder
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
