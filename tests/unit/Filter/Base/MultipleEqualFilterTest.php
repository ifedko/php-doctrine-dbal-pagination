<?php

namespace Ifedko\DoctrineDbalPagination\Test\Filter\Base;

use Mockery;
use Ifedko\DoctrineDbalPagination\Filter\Base\MultipleEqualFilter;
use PHPUnit\Framework\TestCase;

class MultipleEqualFilterTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testApplyReturnQueryBuilderSuccess()
    {
        $queryBuilderMock = Mockery::mock('Doctrine\DBAL\Query\QueryBuilder')
            ->makePartial();

        $multipleEqualFilter = new MultipleEqualFilter('field');
        $multipleEqualFilter->bindValues(['value1', 'value2']);
        $queryBuilder = $multipleEqualFilter->apply($queryBuilderMock);

        $this->assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $queryBuilder);
    }

    public function testSeveralFilters()
    {
        $queryBuilderMock = Mockery::mock('Doctrine\DBAL\Query\QueryBuilder')
            ->makePartial();

        $multipleEqualFilterOne = new MultipleEqualFilter('field_one');
        $multipleEqualFilterOne->bindValues(['value1', 'value2']);
        $queryBuilder = $multipleEqualFilterOne->apply($queryBuilderMock);

        $multipleEqualFilterTwo = new MultipleEqualFilter('field_two');
        $multipleEqualFilterTwo->bindValues([1, 2]);
        $queryBuilder = $multipleEqualFilterTwo->apply($queryBuilder);

        self::assertCount(2, $queryBuilder->getParameters());
        self::assertContains(['value1', 'value2'], $queryBuilder->getParameters());
        self::assertContains([1, 2], $queryBuilder->getParameters());
    }
}
