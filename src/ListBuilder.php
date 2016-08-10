<?php

namespace Ifedko\DoctrineDbalPagination;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

abstract class ListBuilder
{
    /**
     * @var Connection
     */
    protected $dbConnection;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var array
     */
    protected $sortings;

    /**
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
        $this->filters = [];
        $this->sortings = [];
    }

    /**
     * @param array $parameters
     */
    public function configure($parameters)
    {
        $this->configureFilters($parameters);
        $this->configureSorting($parameters);
    }

    /**
     * @return QueryBuilder
     */
    public function query()
    {
        $queryBuilder = $this->baseQuery();
        $queryBuilder = $this->applyFilters($queryBuilder);
        $queryBuilder = $this->applySortings($queryBuilder);
        return $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    public function totalQuery()
    {
        $queryBuilder = $this->baseQuery();
        $queryBuilder = $this->applyFilters($queryBuilder);
        return $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    abstract protected function baseQuery();

    /**
     * @param array $parameters
     * @return $this
     */
    protected function configureFilters($parameters)
    {
        return $this;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    protected function configureSorting($parameters)
    {
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return new QueryBuilder($this->dbConnection);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    private function applyFilters(QueryBuilder $queryBuilder)
    {
        if (!empty($this->filters)) {
            /* @var $filter FilterInterface */
            foreach ($this->filters as $filter) {
                $queryBuilder = $filter->apply($queryBuilder);
            }
        }

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    private function applySortings(QueryBuilder $queryBuilder)
    {
        if (is_array($this->sortings)) {
            foreach ($this->sortings as $field => $direction) {
                if ($direction instanceof SortingInterface) {
                    $direction->apply($queryBuilder);
                } else {
                    $queryBuilder->addOrderBy($field, $direction);
                }
            }
        }

        return $queryBuilder;
    }
}
