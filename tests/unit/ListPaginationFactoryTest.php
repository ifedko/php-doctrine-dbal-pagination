<?php

namespace Ifedko\DoctrineDbalPagination\Test;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Ifedko\DoctrineDbalPagination\Exception\ListPaginationFactoryException;
use Ifedko\DoctrineDbalPagination\ListPagination;
use Ifedko\DoctrineDbalPagination\ListPaginationFactory;
use Ifedko\DoctrineDbalPagination\SortingInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class ListPaginationFactoryTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testCreateByLogIOSListBuilderTypeSuccess()
    {
        $dbConnectionMock = self::createDbConnectionMock();
        $listBuilderClassName = TestListBuilder::class;

        $listPagination = ListPaginationFactory::create($dbConnectionMock, $listBuilderClassName);

        $this->assertInstanceOf(ListPagination::class, $listPagination);
    }

    public function testCreateByLogIOSListBuilderTypeWithLowerCaseInTypeNameSuccess()
    {
        $dbConnectionMock = self::createDbConnectionMock();
        $listBuilderClassName = TestListBuilder::class;

        $listPagination = ListPaginationFactory::create($dbConnectionMock, $listBuilderClassName);

        $this->assertInstanceOf(ListPagination::class, $listPagination);
    }

    public function testCreateIfUnknownListBuilderTypeThrowException()
    {
        $dbConnectionMock = self::createDbConnectionMock();
        $listBuilderClassName = 'Ifedko\\DoctrineDbalPagination\\Test\\NonExistingListBuilder';
        $expectedExceptionMessage = sprintf('Unknown list builder class %s', $listBuilderClassName);

        self::expectException(ListPaginationFactoryException::class);
        self::expectExceptionMessage($expectedExceptionMessage);
        ListPaginationFactory::create($dbConnectionMock, $listBuilderClassName);
    }

    public function testSupportsSorting()
    {
        $builder = new TestListBuilder(self::createDbConnectionMock());
        $builder->configure(['sortBy' => 'name']);

        $this->assertStringContainsString('ORDER BY name ASC', $builder->query()->getSQL());
    }

    public function testHasDefaultSorting()
    {
        $builder = new TestListBuilder(self::createDbConnectionMock());
        $builder->configure([]);

        $this->assertStringContainsString('ORDER BY user.created_at DESC', $builder->query()->getSQL());
    }

    public function testSupportsComplexSorting()
    {
        $sortingModel = Mockery::mock(SortingInterface::class);
        $sortingModel->shouldReceive('bindValues')->andReturn([]);
        $sortingModel->shouldReceive('apply')->once();

        $builder = new TestListBuilder(self::createDbConnectionMock());
        $builder->testSortingModel = $sortingModel;

        $builder->configure([]);
        $builder->query();
    }

    public function testProvidesSortingParams()
    {
        $builder = new TestListBuilder(self::createDbConnectionMock());
        $builder->configure([
            'sortBy' => 'name',
            'foo' => 'bar',
            'sortOrder' => 'asc'
        ]);

        $this->assertEquals(
            ['sortBy' => 'name', 'sortOrder' => 'ASC'],
            $builder->sortingParameters()
        );
    }

    private static function createDbConnectionMock()
    {
        $dbConnectionMock =  Mockery::mock(Connection::class);
        $dbConnectionMock->allows('getDatabasePlatform')->andReturn(new PostgreSQLPlatform());

        return $dbConnectionMock;
    }
}
