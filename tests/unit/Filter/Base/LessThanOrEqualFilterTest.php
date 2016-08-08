<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Mockery as m;
use Ifedko\DoctrineDbalPagination\Filter\Base\LessThanOrEqualFilter;

class LessThanOrEqualFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatesGreaterThanOrEqualCondition()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $this->assertContains(
            "table.endDate <= '2015-09-01'",
            (new LessThanOrEqualFilter('table.endDate'))
                ->bindValues('2015-09-01')
                ->apply($queryBuilder)->getSQL()
        );

    }
}
