<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Test\QueryBuilderTestCase;
use Ifedko\DoctrineDbalPagination\Filter\Base\DateRangeFilter;

class DateRangeFilterTest extends QueryBuilderTestCase
{
    public function testApplyReturnQueryBuilderSuccess(): void
    {
        $dateRangeFilter = new DateRangeFilter('field');
        $dateRangeFilter->bindValues(['begin' => '2015-10-01 00:00:00', 'end' => '2015-10-31 23:59:59']);
        $queryBuilder = $dateRangeFilter->apply(static::$queryBuilder);

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
    }
}
