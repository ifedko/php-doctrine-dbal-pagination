<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Mockery as m;
use Ifedko\DoctrineDbalPagination\Filter\Base\GreaterThanOrEqualFilter;

class GreaterThanOrEqualFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatesGreaterThanOrEqualCondition()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $this->assertContains(
            "table.startDate >= '2015-09-01'",
            (new GreaterThanOrEqualFilter('table.startDate'))
                ->bindValues('2015-09-01')
                ->apply($queryBuilder)->getSQL()
        );

    }
}
