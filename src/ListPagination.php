<?php

namespace Ifedko\DoctrineDbalPagination;

use Doctrine\DBAL\Connection;
use Ifedko\DoctrineDbalPagination\DbAdapter;
use Ifedko\DoctrineDbalPagination\ListBuilder;

class ListPagination
{
    const DEFAULT_LIMIT = 20;
    const DEFAULT_OFFSET = 0;

    /**
     * @var \Ifedko\DoctrineDbalPagination\ListBuilder
     */
    private $listQueryBuilder;

    /**
     * @param \Ifedko\DoctrineDbalPagination\ListBuilder $listQueryBuilder
     */
    public function __construct(ListBuilder $listQueryBuilder)
    {
        $this->listQueryBuilder = $listQueryBuilder;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get($limit, $offset)
    {
        $limit = (intval($limit) > 0) ? intval($limit) : self::DEFAULT_LIMIT;
        $offset = (intval($offset) >= 0) ? $offset : self::DEFAULT_OFFSET;

        $dbAdapter = new DbAdapter();

        $totalQueryBuilder = $this->listQueryBuilder->totalQuery();
        $queryBuilder = $this->listQueryBuilder->query();

        return [
            'total' => $dbAdapter->matchingTotal($totalQueryBuilder),
            'items' => $dbAdapter->matching($queryBuilder, $limit, $offset),
        ];
    }
}
