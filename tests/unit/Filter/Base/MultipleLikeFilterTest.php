<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\Base\MultipleLikeFilter;
use Ifedko\DoctrineDbalPagination\Test\QueryBuilderTestCase;

class MultipleLikeFilterTest extends QueryBuilderTestCase
{
    public function testApplyWithSingleColumnReturnQueryBuilderSuccess(): void
    {
        $likeFilter = new MultipleLikeFilter('field');
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
        $this->assertStringContainsString(
            "(field LIKE '%something%') AND (field LIKE '%like%')",
            $queryBuilder->getSQL()
        );
    }

    public function testApplyNotContainsWordWithSingleColumnReturnQueryBuilderSuccess(): void
    {
        $likeFilter = new MultipleLikeFilter('field');
        $likeFilter->bindValues('something -like');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);

        $this->assertStringContainsString(
            "(field LIKE '%something%') AND (COALESCE(field, '') NOT LIKE '%like%')",
            $queryBuilder->getSQL()
        );
    }

    public function testApplyWithArrayOfColumnsReturnQueryBuilderSuccess(): void
    {
        $likeFilter = new MultipleLikeFilter(['field1', 'field2']);
        $likeFilter->bindValues('w1 w2');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);

        $this->assertStringContainsString(
            "((field1 LIKE '%w1%') OR (field2 LIKE '%w1%')) AND ((field1 LIKE '%w2%') OR (field2 LIKE '%w2%'))",
            $queryBuilder->getSQL()
        );
    }

    public function testAcceptsOperatorOption(): void
    {
        $likeFilter = new MultipleLikeFilter('field', ['operator' => 'ILIKE']);
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);

        $this->assertStringContainsString(
            "(field ILIKE '%something%') AND (field ILIKE '%like%')",
            $queryBuilder->getSQL()
        );
    }

    public function testAcceptsMatchFromStartOption(): void
    {
        $likeFilter = new MultipleLikeFilter(
            ['name', 'email'],
            ['operator' => 'ILIKE', 'matchFromStart' => ['name']]
        );
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);

        $this->assertStringContainsString("(name ILIKE 'something%')", $queryBuilder->getSQL());
        $this->assertStringContainsString("(email ILIKE '%something%')", $queryBuilder->getSQL());
    }

    public function testSupportsSearchByZeroSymbol(): void
    {
        $likeFilter = new MultipleLikeFilter(
            ['name'],
            ['operator' => 'ILIKE']
        );
        $likeFilter->bindValues('0');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);

        self::assertStringContainsString("name ILIKE '%0%'", $queryBuilder->getSQL());
    }

    public function testDoNotApplyMinusSymbolWithoutExcludingWord(): void
    {
        $likeFilter = new MultipleLikeFilter('field');
        $likeFilter->bindValues('something - like');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);

        $this->assertStringNotContainsString('COALESCE', $queryBuilder->getSQL());
    }
}
