<?php

namespace Ifedko\DoctrineDbalPagination\Test;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;

class QueryBuilderTestCase extends \PHPUnit\Framework\TestCase
{
    protected static QueryBuilder $queryBuilder;

    protected static Connection $connection;

    protected const TABLE_NAME = 'users';

    public function setUp(): void
    {
        parent::setUp();

        $config = new Configuration();
        $config->setSchemaManagerFactory(new DefaultSchemaManagerFactory());

        self::$connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:'
        ], $config);
        static::$connection->executeQuery(
            'CREATE TABLE ' . self::TABLE_NAME . ' (id INTEGER, name VARCHAR(255), created_at DATE)'
        );

        self::$queryBuilder = new QueryBuilder(self::$connection);
    }
}
