<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Ifedko\DoctrineDbalPagination\Filter\Base\EqualFilter;
use Ifedko\DoctrineDbalPagination\Test\QueryBuilderTestCase;

class EqualFilterTest extends QueryBuilderTestCase
{
    public function testCreatesEquationConditionWithInteger(): void
    {
        $this->assertStringContainsString(
            "table.user_id = '12'",
            (new EqualFilter('table.user_id', \PDO::PARAM_INT))
                ->bindValues(12)
                ->apply(static::$queryBuilder)->getSQL()
        );
    }

    public function testCreatesEquationConditionWithString(): void
    {
        $this->assertStringContainsString(
            "table.login = 'xiag'",
            (new EqualFilter('table.login', \PDO::PARAM_STR))
                ->bindValues('xiag')
                ->apply(static::$queryBuilder)->getSQL()
        );
    }

    public function testConvertsInputValueToIntegerIfNeeded(): void
    {
        $this->assertStringContainsString(
            "table.user_id = '12'",
            (new EqualFilter('table.user_id', \PDO::PARAM_INT))
                ->bindValues('12not-an-integer')
                ->apply(static::$queryBuilder)->getSQL()
        );
    }
}
