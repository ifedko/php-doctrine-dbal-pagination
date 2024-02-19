<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Test\QueryBuilderTestCase;
use Ifedko\DoctrineDbalPagination\Filter\Base\MultipleEqualFilter;

class MultipleEqualFilterTest extends QueryBuilderTestCase
{
    public function testApplyReturnQueryBuilderSuccess(): void
    {
        $multipleEqualFilter = new MultipleEqualFilter('field');
        $multipleEqualFilter->bindValues(['value1', 'value2']);
        $queryBuilder = $multipleEqualFilter->apply(static::$queryBuilder);

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
    }

    public function testSeveralFilters(): void
    {
        $queryBuilder = static::$queryBuilder;

        $multipleEqualFilterOne = new MultipleEqualFilter('field_one');
        $multipleEqualFilterOne->bindValues(['value1', 'value2']);
        $queryBuilder = $multipleEqualFilterOne->apply($queryBuilder);

        $multipleEqualFilterTwo = new MultipleEqualFilter('field_two');
        $multipleEqualFilterTwo->bindValues([1, 2]);
        $queryBuilder = $multipleEqualFilterTwo->apply($queryBuilder);

        self::assertCount(2, $queryBuilder->getParameters());
        self::assertContains(['value1', 'value2'], $queryBuilder->getParameters());
        self::assertContains([1, 2], $queryBuilder->getParameters());
    }
}
