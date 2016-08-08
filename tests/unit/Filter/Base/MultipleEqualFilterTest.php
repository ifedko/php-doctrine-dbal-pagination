<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Mockery;
use Ifedko\DoctrineDbalPagination\Filter\Base\MultipleEqualFilter;

class MultipleEqualFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyReturnQueryBuilderSuccess()
    {
        $queryBuilderMock = Mockery::mock('Doctrine\DBAL\Query\QueryBuilder')
            ->makePartial();

        $multipleEqualFilter = new MultipleEqualFilter('field');
        $multipleEqualFilter->bindValues(['value1', 'value2']);
        $queryBuilder = $multipleEqualFilter->apply($queryBuilderMock);

        $this->assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $queryBuilder);
    }
}
