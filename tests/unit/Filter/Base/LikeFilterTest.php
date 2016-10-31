<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Mockery;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\Base\LikeFilter;

class LikeFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyWithSingleColumnsReturnQueryBuilderSuccess()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $likeFilter = new LikeFilter('field');
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply($queryBuilder);
        $this->assertContains(
            "field LIKE '%something like%'",
            $queryBuilder->getSQL()
        );

        $this->assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $queryBuilder);
    }

    public function testApplyWithArrayOfColumnsReturnQueryBuilderSuccess()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $likeFilter = new LikeFilter(['field1', 'field2']);
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply($queryBuilder);
        $this->assertContains(
            "(field1 LIKE '%something like%') OR (field2 LIKE '%something like%')",
            $queryBuilder->getSQL()
        );

        $this->assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $queryBuilder);
    }

    public function testAcceptsOperatorOption()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $likeFilter = new LikeFilter('field', ['operator' => 'ILIKE']);
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply($queryBuilder);
        $this->assertContains(
            "field ILIKE '%something like%'",
            $queryBuilder->getSQL()
        );

        $this->assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $queryBuilder);
    }

}
