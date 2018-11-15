<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\Base\MultipleLikeFilter;

class MultipleLikeFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyWithSingleColumnReturnQueryBuilderSuccess()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $likeFilter = new MultipleLikeFilter('field');
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply($queryBuilder);
        $this->assertContains(
            "(field LIKE '%something%') AND (field LIKE '%like%')",
            $queryBuilder->getSQL()
        );

        $this->assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $queryBuilder);
    }

    public function testApplyNotContainsWordWithSingleColumnReturnQueryBuilderSuccess()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $likeFilter = new MultipleLikeFilter('field');
        $likeFilter->bindValues('something -like');
        $queryBuilder = $likeFilter->apply($queryBuilder);
        $this->assertContains(
            "(field LIKE '%something%') AND (COALESCE(field, '') NOT LIKE '%like%')",
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

        $likeFilter = new MultipleLikeFilter(['field1', 'field2']);
        $likeFilter->bindValues('w1 w2');
        $queryBuilder = $likeFilter->apply($queryBuilder);
        $this->assertContains(
            "((field1 LIKE '%w1%') OR (field2 LIKE '%w1%')) AND ((field1 LIKE '%w2%') OR (field2 LIKE '%w2%'))",
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

        $likeFilter = new MultipleLikeFilter('field', ['operator' => 'ILIKE']);
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply($queryBuilder);
        $this->assertContains(
            "(field ILIKE '%something%') AND (field ILIKE '%like%')",
            $queryBuilder->getSQL()
        );

        $this->assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $queryBuilder);
    }

    public function testAcceptsMatchFromStartOption()
    {
        $queryBuilder = new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));

        $likeFilter = new MultipleLikeFilter(
            ['name', 'email'],
            ['operator' => 'ILIKE', 'matchFromStart' => ['name']]
        );
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply($queryBuilder);

        $this->assertContains(
            "(name ILIKE 'something%')",
            $queryBuilder->getSQL()
        );

        $this->assertContains(
            "(email ILIKE '%something%')",
            $queryBuilder->getSQL()
        );

    }

}
