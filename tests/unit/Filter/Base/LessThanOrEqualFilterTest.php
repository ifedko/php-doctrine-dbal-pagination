<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\Base\LessThanOrEqualFilter;
use PHPUnit\Framework\TestCase;

class LessThanOrEqualFilterTest extends TestCase
{
    public function testCreatesGreaterThanOrEqualCondition()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $this->assertStringContainsString(
            "table.endDate <= '2015-09-01'",
            (new LessThanOrEqualFilter('table.endDate'))
                ->bindValues('2015-09-01')
                ->apply($queryBuilder)->getSQL()
        );

    }
}
