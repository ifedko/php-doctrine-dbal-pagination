<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Ifedko\DoctrineDbalPagination\Filter\Base\LessThanOrEqualFilter;
use Ifedko\DoctrineDbalPagination\Test\QueryBuilderTestCase;

class LessThanOrEqualFilterTest extends QueryBuilderTestCase
{
    public function testCreatesGreaterThanOrEqualCondition(): void
    {
        $this->assertStringContainsString(
            "table.endDate <= '2015-09-01'",
            (new LessThanOrEqualFilter('table.endDate'))
                ->bindValues('2015-09-01')
                ->apply(static::$queryBuilder)->getSQL()
        );

    }
}
