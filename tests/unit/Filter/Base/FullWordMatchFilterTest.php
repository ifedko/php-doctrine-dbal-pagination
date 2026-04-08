<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Ifedko\DoctrineDbalPagination\Filter\Base\FullWordMatchFilter;
use Ifedko\DoctrineDbalPagination\Test\QueryBuilderTestCase;

class FullWordMatchFilterTest extends QueryBuilderTestCase
{
    public function testApplyCreatesEqualsCondition(): void
    {
        $fullWordMatchFilter = new FullWordMatchFilter('field');
        $fullWordMatchFilter->bindValues('12');
        $queryBuilder = $fullWordMatchFilter->apply(static::$queryBuilder);

        $this->assertSame(static::$queryBuilder, $queryBuilder);
        $this->assertStringContainsString("field = '12'", $queryBuilder->getSQL());
    }

    public function testApplyTrimsBoundValue(): void
    {
        $fullWordMatchFilter = new FullWordMatchFilter('field');
        $fullWordMatchFilter->bindValues('  value  ');
        $queryBuilder = $fullWordMatchFilter->apply(static::$queryBuilder);

        $this->assertStringContainsString("field = 'value'", $queryBuilder->getSQL());
    }
}
