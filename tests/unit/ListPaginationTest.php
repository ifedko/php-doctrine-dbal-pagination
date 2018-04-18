<?php

namespace Ifedko\DoctrineDbalPagination\Test;

use Mockery;
use Ifedko\DoctrineDbalPagination\ListPagination;

class ListPaginationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetWithCorrectLimitAndOffset()
    {
        $limit = 2;
        $offset = 0;
        $expectedTotal = 15;
        $expectedItems = [
            ['id' => 1, 'name' => 'name1'],
            ['id' => 2, 'name' => 'name2']
        ];
        $listBuilder = self::createListBuilderMock($expectedTotal, $expectedItems);

        $listPagination = new ListPagination($listBuilder);
        $listPage = $listPagination->get($limit, $offset);

        $this->assertEquals($expectedTotal, $listPage['total']);
        $this->assertEquals($expectedItems, $listPage['items']);
    }

    public function testGetWithNotCorrectLimitAndOffset()
    {
        $limit = 'any limit';
        $offset = 'any offset';
        $expectedTotal = 15;
        $expectedItems = [
            ['id' => 1, 'name' => 'name1'],
            ['id' => 2, 'name' => 'name2']
        ];
        $listBuilder = self::createListBuilderMock($expectedTotal, $expectedItems);

        $listPagination = new ListPagination($listBuilder);
        $listPage = $listPagination->get($limit, $offset);

        $this->assertEquals($expectedTotal, $listPage['total']);
        $this->assertEquals($expectedItems, $listPage['items']);
    }

    public function testItIsPossibleToDefineAMapFunctionToApplyToItemsOfAPage()
    {
        $listBuilder = self::createListBuilderMock(
            1,
            [
                ['id' => 1, 'object' => '{"some":"json"}']
            ]
        );

        $pager = new ListPagination($listBuilder);
        $pager->definePageItemsMapCallback(function ($row) {
            return array_merge($row, ['object' => json_decode($row['object'], true)]);
        });

        $this->assertEquals(
            [
                ['id' => 1, 'object' => ['some' => 'json']]
            ],
            $pager->get(10, 0)['items']
        );
    }

    private static function createListBuilderMock($expectedTotal, $expectedItems)
    {
        $statementMock = Mockery::mock('\Doctrine\DBAL\Statement', [
            'rowCount' => $expectedTotal,
            'fetchAll' => $expectedItems
        ]);

        $queryBuilderMock = Mockery::mock('\Doctrine\DBAL\Query\QueryBuilder');
        $queryBuilderMock->shouldReceive('execute')->andReturn($statementMock);
        $queryBuilderMock->shouldReceive('setMaxResults')->andReturn($queryBuilderMock);
        $queryBuilderMock->shouldReceive('setFirstResult')->andReturn($queryBuilderMock);

        $listBuilderMock = Mockery::mock('\Ifedko\DoctrineDbalPagination\ListBuilder');
        $listBuilderMock->shouldReceive('totalQuery')->andReturn($queryBuilderMock);
        $listBuilderMock->shouldReceive('query')->andReturn($queryBuilderMock);
        $listBuilderMock->shouldReceive('sortingParameters')->andReturn([]);

        return $listBuilderMock;
    }
}
