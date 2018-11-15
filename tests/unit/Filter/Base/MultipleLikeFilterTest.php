<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\Base\MultipleLikeFilter;

class MultipleLikeFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyWithSingleColumnReturnQueryBuilderSuccess()
    {
        $likeFilter = new MultipleLikeFilter('field');
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply(self::queryBuilder());

        $this->assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $queryBuilder);
        $this->assertContains(
            "(field LIKE '%something%') AND (field LIKE '%like%')",
            $queryBuilder->getSQL()
        );
    }

    public function testApplyNotContainsWordWithSingleColumnReturnQueryBuilderSuccess()
    {
        $likeFilter = new MultipleLikeFilter('field');
        $likeFilter->bindValues('something -like');
        $queryBuilder = $likeFilter->apply(self::queryBuilder());
        $this->assertContains(
            "(field LIKE '%something%') AND (COALESCE(field, '') NOT LIKE '%like%')",
            $queryBuilder->getSQL()
        );
    }

    public function testApplyWithArrayOfColumnsReturnQueryBuilderSuccess()
    {
        $likeFilter = new MultipleLikeFilter(['field1', 'field2']);
        $likeFilter->bindValues('w1 w2');
        $queryBuilder = $likeFilter->apply(self::queryBuilder());
        $this->assertContains(
            "((field1 LIKE '%w1%') OR (field2 LIKE '%w1%')) AND ((field1 LIKE '%w2%') OR (field2 LIKE '%w2%'))",
            $queryBuilder->getSQL()
        );
    }

    public function testAcceptsOperatorOption()
    {
        $likeFilter = new MultipleLikeFilter('field', ['operator' => 'ILIKE']);
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply(self::queryBuilder());
        $this->assertContains(
            "(field ILIKE '%something%') AND (field ILIKE '%like%')",
            $queryBuilder->getSQL()
        );
    }

    public function testAcceptsMatchFromStartOption()
    {
        $likeFilter = new MultipleLikeFilter(
            ['name', 'email'],
            ['operator' => 'ILIKE', 'matchFromStart' => ['name']]
        );
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply(self::queryBuilder());

        $this->assertContains("(name ILIKE 'something%')", $queryBuilder->getSQL());
        $this->assertContains("(email ILIKE '%something%')", $queryBuilder->getSQL());

    }

    /**
     * @return QueryBuilder
     * @throws \Doctrine\DBAL\DBALException
     */
    private static function queryBuilder()
    {
        return new QueryBuilder(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ]));
    }

}
