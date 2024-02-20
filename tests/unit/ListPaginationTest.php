<?php

namespace Ifedko\DoctrineDbalPagination\Test;

use Ifedko\DoctrineDbalPagination\ListPagination;
use Mockery;

class ListPaginationTest extends QueryBuilderTestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testGetWithCorrectLimitAndOffset(): void
    {
        $limit = 2;
        $offset = 0;
        $expectedTotal = 2;
        $expectedItems = [
            ['id' => 1, 'name' => 'name1', 'created_at' => null],
            ['id' => 2, 'name' => 'name2', 'created_at' => null]
        ];

        foreach ($expectedItems as $item) {
            static::$connection->insert(self::TABLE_NAME, $item);
        }

        $listBuilder = new TestListBuilder(static::$connection);
        $listPagination = new ListPagination($listBuilder);

        $listPage = $listPagination->get($limit, $offset);

        $this->assertEquals($expectedTotal, $listPage['total']);
        $this->assertEquals($expectedItems, $listPage['items']);
    }

    public function testGetWithNotCorrectLimitAndOffset(): void
    {
        $limit = null;
        $offset = -3;
        $expectedTotal = 2;
        $expectedItems = [
            ['id' => 1, 'name' => 'name1', 'created_at' => null],
            ['id' => 2, 'name' => 'name2', 'created_at' => null]
        ];
        foreach ($expectedItems as $item) {
            static::$connection->insert(self::TABLE_NAME, $item);
        }

        $listPagination = new ListPagination(new TestListBuilder(static::$connection));
        $listPage = $listPagination->get($limit, $offset);

        $this->assertEquals($expectedTotal, $listPage['total']);
        $this->assertEquals($expectedItems, $listPage['items']);
    }

    public function testItIsPossibleToDefineAMapFunctionToApplyToItemsOfAPage(): void
    {
        $expectedItems = [
            ['id' => 1, 'name' => '{"some":"json1"}', 'created_at' => null],
            ['id' => 2, 'name' => '{"some":"json2"}', 'created_at' => null]
        ];
        foreach ($expectedItems as $item) {
            static::$connection->insert(self::TABLE_NAME, $item);
        }

        $listPagination = new ListPagination(new TestListBuilder(static::$connection));
        $listPagination->definePageItemsMapCallback(function ($row) {
            return array_merge($row, ['name' => json_decode($row['name'], true)]);
        });

        $this->assertEquals(
            [
                ['id' => 1, 'name' => ['some' => 'json1'], 'created_at' => null],
                ['id' => 2, 'name' => ['some' => 'json2'], 'created_at' => null]
            ],
            $listPagination->get(null, null)['items']
        );
    }
}
