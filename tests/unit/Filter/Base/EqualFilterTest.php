<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Mockery;
use Ifedko\DoctrineDbalPagination\Filter\Base\EqualFilter;

class EqualFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatesEquationConditionWithInteger()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $this->assertContains(
            "table.user_id = '12'",
            (new EqualFilter('table.user_id', \PDO::PARAM_INT))
                ->bindValues(12)
                ->apply($queryBuilder)->getSQL()
        );

    }

    public function testCreatesEquationConditionWithString()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $this->assertContains(
            "table.login = 'xiag'",
            (new EqualFilter('table.login', \PDO::PARAM_STR))
                ->bindValues('xiag')
                ->apply($queryBuilder)->getSQL()
        );

    }

    public function testConvertsInputValueToIntegerIfNeeded()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $this->assertContains(
            "table.user_id = '12'",
            (new EqualFilter('table.user_id', \PDO::PARAM_INT))
                ->bindValues('12not-an-integer')
                ->apply($queryBuilder)->getSQL()
        );

    }
}
