<?php

namespace Ifedko\DoctrineDbalPagination\Test\Sorting;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Sorting\ByColumn;
use Ifedko\DoctrineDbalPagination\SortingInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ByColumnTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testConfiguresSortingInQueryBuilder(): void
    {
        $builder = \Mockery::mock(QueryBuilder::class);
        $builder->shouldReceive('addOrderBy')->with('t.name', 'DESC')->once();

        self::sortingByNameWithParameters(['sortBy' => 'name', 'sortOrder' => 'desc'])->apply($builder);
    }

    public function testSortingCanBeDefinedWithoutTheDirection(): void
    {
        $builder = \Mockery::mock(QueryBuilder::class);
        $builder->shouldReceive('addOrderBy')->with('t.name', null)->once();

        self::sortingByNameWithParameters(['sortBy' => 'name'])->apply($builder);
    }

    public function testDoesNoSortingWhenNoParametersWereGiven(): void
    {
        $builder = \Mockery::mock(QueryBuilder::class);
        $builder->shouldReceive('addOrderBy')->never();

        self::sortingByNameWithParameters([])->apply($builder);
    }

    public function testWillIgnoreUnknownParameters(): void
    {
        $builder = \Mockery::mock(QueryBuilder::class);
        $builder->shouldReceive('addOrderBy')->never();

        self::sortingByNameWithParameters(['sortBy' => 'table.evil'])->apply($builder);
    }

    public function testPermanentDefaultSortingCanBeGiven(): void
    {
        $builder = \Mockery::mock(QueryBuilder::class);
        $builder->shouldReceive('addOrderBy')->with('t.name', 'DESC')->once();

        self::sortingByNameWithParameters([], 'DESC')->apply($builder);
    }

    public function testPermanentDefaultSortingIsActiveEvenWhenSortingForOtherColumnIsRequested(): void
    {
        $builder = \Mockery::mock(QueryBuilder::class);
        $builder->shouldReceive('addOrderBy')->with('t.name', 'DESC')->once();

        self::sortingByNameWithParameters(['sortBy' => 'someColumn', 'sortOrder' => 'ASC'], 'DESC')->apply($builder);
    }

    public function testPermanentDefaultSortingCanBeOverridden(): void
    {
        $builder = \Mockery::mock(QueryBuilder::class);
        $builder->shouldReceive('addOrderBy')->with('t.name', 'ASC')->once();

        self::sortingByNameWithParameters(['sortBy' => 'name', 'sortOrder' => 'ASC'], 'DESC')->apply($builder);
    }

    public function testReturnsSortingParametersThatActuallyWereApplied(): void
    {
        $sorting = new ByColumn('name', 't.name');

        $this->assertSame(
            ['sortBy' => 'name', 'sortOrder' => 'DESC'],
            $sorting->bindValues(['foo' => 'bar', 'sortBy' => 'name', 'sortOrder' => 'desc'])
        );
    }

    private static function sortingByNameWithParameters($parameters, $defaultDirection = null): SortingInterface
    {
        $sorting = new ByColumn('name', 't.name', $defaultDirection);
        $sorting->bindValues($parameters);

        return $sorting;
    }
}
