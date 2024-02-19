<?php

namespace Ifedko\DoctrineDbalPagination\Test;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\ListBuilder;
use Mockery;
use PHPUnit\Framework\TestCase;

class ListBuilderTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testConfigureSuccess(): void
    {
        $dbConnection = self::createDbConnectionMock();
        $parameters = ['param1' => 'value1', 'sortBy' => 'field1'];

        $listBuilderMock = Mockery::mock(ListBuilder::class, [$dbConnection])->makePartial();
        $listBuilderMock->configure($parameters);
    }

    public function testQueryReturnQueryBuilderSuccess(): void
    {
        $dbConnection = self::createDbConnectionMock();
        $parameters = ['param1' => 'value1', 'sortBy' => 'field1'];

        $queryBuilderMock = Mockery::mock(QueryBuilder::class);
        $listBuilderMock = Mockery::mock(ListBuilder::class, [$dbConnection])->makePartial();
        $listBuilderMock->shouldAllowMockingProtectedMethods();
        $listBuilderMock->expects('baseQuery')->andReturn($queryBuilderMock);

        $listBuilderMock->configure($parameters);
        $queryBuilder = $listBuilderMock->query();

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
    }

    public function testTotalQueryReturnQueryBuilderSuccess(): void
    {
        $dbConnection = self::createDbConnectionMock();
        $parameters = ['param1' => 'value1', 'sortBy' => 'field1'];
        $queryBuilderMock = Mockery::mock(QueryBuilder::class);
        $queryBuilderMock
            ->shouldReceive('resetQueryPart')
            ->andReturn($queryBuilderMock);

        $queryBuilderMock
            ->shouldReceive('select')
            ->andReturn($queryBuilderMock);

        $listBuilderMock = Mockery::mock(ListBuilder::class, [$dbConnection])->makePartial();
        $listBuilderMock->shouldAllowMockingProtectedMethods();
        $listBuilderMock->shouldReceive('baseQuery')->andReturn($queryBuilderMock);

        $listBuilderMock->configure($parameters);
        $queryBuilder = $listBuilderMock->totalQuery();

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
    }

    public function testTotalQueryResetSelectPart(): void
    {
        $dbConnection = self::createDbConnectionMock();
        $queryBuilderMock = Mockery::mock(QueryBuilder::class);
        $queryBuilderMock->expects('select')->with('count(*)')->andReturn($queryBuilderMock);

        $listBuilderMock = Mockery::mock(ListBuilder::class, [$dbConnection])->makePartial();
        $listBuilderMock->shouldAllowMockingProtectedMethods();
        $listBuilderMock->expects('baseQuery')->andReturn($queryBuilderMock);

        $listBuilderMock->totalQuery();
    }

    private static function createDbConnectionMock(): Connection
    {
        return Mockery::mock(Connection::class);
    }
}
