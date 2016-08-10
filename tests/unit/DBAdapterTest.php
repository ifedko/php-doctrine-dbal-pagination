<?php

namespace Ifedko\DoctrineDbalPagination\Test;

use Mockery;
use Ifedko\DoctrineDbalPagination\DbAdapter;

class DbAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchingReturnArraySuccess()
    {
        $limit = 10;
        $offset = 0;
        $dbStatement = self::createDbStatementMock();
        $dbStatement->shouldReceive('fetchAll')->andReturn([
            ['id' => 1, 'name' => 'name1'],
            ['id' => 2, 'name' => 'name2'],
        ]);
        $queryBuilderMock = self::createQueryBuilderMock();
        $queryBuilderMock->shouldReceive('setMaxResults')->andReturn($queryBuilderMock);
        $queryBuilderMock->shouldReceive('setFirstResult')->andReturn($queryBuilderMock);
        $queryBuilderMock->shouldReceive('execute')->andReturn($dbStatement);

        $dbAdapter = new DbAdapter();
        $items = $dbAdapter->matching($queryBuilderMock, $limit, $offset);

        $this->assertTrue(is_array($items));
    }

    public function testMatchingTotalReturnIntSuccess()
    {
        $dbStatement = self::createDbStatementMock();
        $dbStatement->shouldReceive('rowCount')->andReturn(2);
        $queryBuilderMock = self::createQueryBuilderMock();
        $queryBuilderMock->shouldReceive('setMaxResults')->andReturn($queryBuilderMock);
        $queryBuilderMock->shouldReceive('setFirstResult')->andReturn($queryBuilderMock);
        $queryBuilderMock->shouldReceive('execute')->andReturn($dbStatement);

        $dbAdapter = new DbAdapter();
        $total = $dbAdapter->matchingTotal($queryBuilderMock);

        $this->assertTrue(is_int($total));
    }

    private static function createDbStatementMock()
    {
        return Mockery::mock('Doctrine\DBAL\Statement');
    }

    private static function createQueryBuilderMock()
    {
        $queryBuilderMock = Mockery::mock('Doctrine\DBAL\Query\QueryBuilder');
        return $queryBuilderMock;
    }
}
