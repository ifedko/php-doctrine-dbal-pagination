<?php

namespace Ifedko\DoctrineDbalPagination;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\FilterInterface;

abstract class ListBuilder
{
    protected Connection $dbConnection;

    protected ?array $filters;

    protected ?array $sortings;

    protected array $sortingParameters = [];

    /**
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
        $this->filters = [];
        $this->sortings = [];
    }

    public function configure(array $parameters): void
    {
        $this->filters = [];
        $this->configureFilters($parameters);

        $this->sortings = [];
        $this->configureSorting($parameters);
    }

    public function query(): QueryBuilder
    {
        $queryBuilder = $this->baseQuery();
        $queryBuilder = $this->applyFilters($queryBuilder);
        $queryBuilder = $this->applySortings($queryBuilder);

        return $queryBuilder;
    }

    public function totalQuery(): QueryBuilder
    {
        $queryBuilder = (clone $this->baseQuery())->select('count(*)');
        $queryBuilder = $this->applyFilters($queryBuilder);

        return $queryBuilder;
    }

    public function sortingParameters(): array
    {
        return $this->sortingParameters;
    }

    abstract protected function baseQuery(): QueryBuilder;

    protected function configureFilters(array $parameters): self
    {
        return $this;
    }

    protected function configureSorting(array $parameters):self
    {
        return $this;
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->dbConnection);
    }

    protected function sortUsing(SortingInterface $sorting, array $parameters): void
    {
        $this->sortingParameters = array_merge(
            $sorting->bindValues($parameters),
            $this->sortingParameters
        );
        $this->sortings[] = $sorting;
    }

    private function applyFilters(QueryBuilder $queryBuilder): QueryBuilder
    {
        /* @var $filter FilterInterface */
        foreach ($this->filters as $filter) {
            $queryBuilder = $filter->apply($queryBuilder);
        }

        return $queryBuilder;
    }

    private function applySortings(QueryBuilder $queryBuilder): QueryBuilder
    {
        foreach ($this->sortings as $sorting) {
            $sorting->apply($queryBuilder);
        }

        return $queryBuilder;
    }
}
