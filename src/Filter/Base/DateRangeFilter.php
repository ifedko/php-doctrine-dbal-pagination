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
        $expressionBuilder = $builder->expr();

        if (!$this->beginValue && !$this->endValue) {
            return $builder;
        }

        $expression = [];
        if ($this->beginValue) {
            $expression[] = $expressionBuilder->gte(
                $this->column,
                $expressionBuilder->literal($this->beginValue)
            );
        }

        if ($this->endValue) {
            $expression[] = $expressionBuilder->lte(
                $this->column,
                $expressionBuilder->literal($this->endValue)
            );
        }

        $builder->andWhere(
            count($expression) > 1 ? $expressionBuilder->and(...$expression) : array_pop($expression)
        );

        return $builder;
    }
}
