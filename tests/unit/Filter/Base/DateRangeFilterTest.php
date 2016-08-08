<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Mockery;
use Ifedko\DoctrineDbalPagination\Filter\Base\DateRangeFilter;

class DateRangeFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyReturnQueryBuilderSuccess()
    {
        $queryBuilderMock = Mockery::mock('Doctrine\DBAL\Query\QueryBuilder')
            ->makePartial();

        $dateRangeFilter = new DateRangeFilter('field');
        $dateRangeFilter->bindValues('2015-10-01 00:00:00', '2015-10-31 23:59:59');
        $queryBuilder = $dateRangeFilter->apply($queryBuilderMock);

        $this->assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $queryBuilder);
    }
}
