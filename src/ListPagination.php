<?php

namespace Ifedko\DoctrineDbalPagination;

class ListPagination
{
    const DEFAULT_LIMIT = 20;
    const DEFAULT_OFFSET = 0;

    private ListBuilder $listQueryBuilder;

    /**
     * @var callable|null
     */
    private $pageItemsMapCallback;

    public function __construct(ListBuilder $listQueryBuilder)
    {
        $this->listQueryBuilder = $listQueryBuilder;
    }

    public function get(?int $limit, ?int $offset): array
    {
        $limit = (intval($limit) > 0) ? intval($limit) : self::DEFAULT_LIMIT;
        $offset = (intval($offset) >= 0) ? intval($offset) : self::DEFAULT_OFFSET;

        $queryBuilder = $this->listQueryBuilder->query();
        $queryBuilder->setMaxResults($limit);
        $queryBuilder->setFirstResult($offset);

        $pageItems = $queryBuilder->execute()->fetchAllAssociative();

        return [
            'total' => $this->listQueryBuilder->totalQuery()
                ->execute()->fetchOne(),

            'items' => is_null($this->pageItemsMapCallback) ?
                $pageItems : array_map($this->pageItemsMapCallback, $pageItems),

            'sorting' => $this->listQueryBuilder->sortingParameters()
        ];
    }

    public function definePageItemsMapCallback($callback): void
    {
        $this->pageItemsMapCallback = $callback;
    }
}
