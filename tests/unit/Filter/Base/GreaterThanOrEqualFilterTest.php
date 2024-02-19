<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Ifedko\DoctrineDbalPagination\Filter\Base\GreaterThanOrEqualFilter;
use Ifedko\DoctrineDbalPagination\Test\QueryBuilderTestCase;

class GreaterThanOrEqualFilterTest extends QueryBuilderTestCase
{
    public function testCreatesGreaterThanOrEqualCondition(): void
    {
        $this->assertStringContainsString(
            "table.startDate >= '2015-09-01'",
            (new GreaterThanOrEqualFilter('table.startDate'))
                ->bindValues('2015-09-01')
                ->apply(static::$queryBuilder)->getSQL()
        );

    }
}
