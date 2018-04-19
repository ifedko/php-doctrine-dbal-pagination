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
     * @var array
     */
    protected $sortingParameters = [];

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
        $this->filters = [];
        $this->configureFilters($parameters);

        $this->sortings = [];
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
     * @return array of sorting parameter that were applied to the list
     */
    public function sortingParameters()
    {
        return $this->sortingParameters;
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
     * @param $sorting SortingInterface
     * @param array $parameters
     */
    protected function sortUsing(SortingInterface $sorting, array $parameters)
    {
        $this->sortingParameters = array_merge(
            $sorting->bindValues($parameters),
            $this->sortingParameters
        );
        $this->sortings[] = $sorting;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    private function applyFilters(QueryBuilder $queryBuilder)
    {
        /* @var $filter FilterInterface */
        foreach ($this->filters as $filter) {
            $queryBuilder = $filter->apply($queryBuilder);
        }

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    private function applySortings(QueryBuilder $queryBuilder)
    {
        foreach ($this->sortings as $sorting) {
            $sorting->apply($queryBuilder);
        }

        return $queryBuilder;
    }
}
