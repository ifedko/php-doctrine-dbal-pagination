<?php

namespace Ifedko\DoctrineDbalPagination\Test;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\Base\EqualFilter;
use Ifedko\DoctrineDbalPagination\Filter\Base\GreaterThanOrEqualFilter;
use Ifedko\DoctrineDbalPagination\Filter\Base\LessThanOrEqualFilter;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;
use Ifedko\DoctrineDbalPagination\ListBuilder;
use Ifedko\DoctrineDbalPagination\Sorting\ByColumn;
use Ifedko\DoctrineDbalPagination\SortingInterface;

class TestListBuilder extends ListBuilder
{
    public SortingInterface $testSortingModel;

    protected function configureSorting($parameters): self
    {
        if (isset($this->testSortingModel)) {
            $this->sortUsing($this->testSortingModel, $parameters);
        }

        $this->sortUsing(new ByColumn('id', 'user_id'), $parameters);
        $this->sortUsing(new ByColumn('name', 'name'), $parameters);
        $this->sortUsing(new ByColumn('created', 'user.created_at', 'DESC'), $parameters);

        return $this;
    }

    protected function configureFilters($parameters): self
    {
        $mapAvailableFilterByParameter = [
            'user_id' => new EqualFilter('id', \PDO::PARAM_INT),
            'name' => new EqualFilter('name', \PDO::PARAM_STR),
            'created_at_from' => new GreaterThanOrEqualFilter('user.created_at'),
            'created_at_to' => new LessThanOrEqualFilter('user.created_at')
        ];

        /* @var $filter FilterInterface */
        foreach ($mapAvailableFilterByParameter as $parameterName => $filter) {
            if (isset($parameters[$parameterName])) {
                $filter->bindValues($parameters[$parameterName]);
                $this->filters[] = $filter;
            }
        }

        return $this;
    }

    protected function baseQuery(): QueryBuilder
    {
        $builder = $this->getQueryBuilder();
        $builder
            ->select('id', 'name', 'created_at')
            ->from('users');

        return $builder;
    }
}
