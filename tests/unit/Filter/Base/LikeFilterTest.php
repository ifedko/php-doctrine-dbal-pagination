<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\Filter\Base\LikeFilter;
use Ifedko\DoctrineDbalPagination\Test\QueryBuilderTestCase;

class LikeFilterTest extends QueryBuilderTestCase
{
    public function testApplyWithSingleColumnsReturnQueryBuilderSuccess(): void
    {
        $likeFilter = new LikeFilter('field');
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
        $this->assertStringContainsString("field LIKE '%something like%'", $queryBuilder->getSQL());

    }

    public function testApplyWithArrayOfColumnsReturnQueryBuilderSuccess(): void
    {
        $likeFilter = new LikeFilter(['field1', 'field2']);
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
        $this->assertStringContainsString(
            "(field1 LIKE '%something like%') OR (field2 LIKE '%something like%')",
            $queryBuilder->getSQL()
        );
    }

    public function testAcceptsOperatorOption(): void
    {
        $likeFilter = new LikeFilter('field', ['operator' => 'ILIKE']);
        $likeFilter->bindValues('something like');
        $queryBuilder = $likeFilter->apply(static::$queryBuilder);
        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);

        $this->assertStringContainsString("field ILIKE '%something like%'", $queryBuilder->getSQL());
    }

}
