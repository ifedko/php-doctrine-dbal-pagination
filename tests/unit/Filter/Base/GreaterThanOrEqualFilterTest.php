<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\Base\GreaterThanOrEqualFilter;
use PHPUnit\Framework\TestCase;

class GreaterThanOrEqualFilterTest extends TestCase
{
    public function testCreatesGreaterThanOrEqualCondition()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $this->assertStringContainsString(
            "table.startDate >= '2015-09-01'",
            (new GreaterThanOrEqualFilter('table.startDate'))
                ->bindValues('2015-09-01')
                ->apply($queryBuilder)->getSQL()
        );

    }
}
