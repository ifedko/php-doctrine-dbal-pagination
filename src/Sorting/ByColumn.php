<?php

namespace Ifedko\DoctrineDbalPagination\Sorting;

use Doctrine\DBAL\Query\QueryBuilder;
use Ifedko\DoctrineDbalPagination\SortingInterface;

class ByColumn implements SortingInterface
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';
    const PARAM_NAME_SORT_BY = 'sortBy';
    const PARAM_NAME_SORT_ORDER = 'sortOrder';

    private $sortColumn;
    private $sortDirection;
    private $columnAlias;
    private $columnName;

    /**
     * @var null|string one of self::SORT_*
     */
    private $defaultDirection;

    public function __construct($columnAlias, $columnName, $defaultDirection = null)
    {
        $this->columnAlias = $columnAlias;
        $this->columnName = $columnName;
        $this->defaultDirection = $defaultDirection;
    }

    /**
     * @param array $values
     * @return array values that were actually used to define sorting
     */
    public function bindValues($values)
    {
        $appliedValues = [];
        $this->sortColumn = null;

        if (isset($values[self::PARAM_NAME_SORT_BY]) && ($values[self::PARAM_NAME_SORT_BY] === $this->columnAlias)) {

            $this->sortColumn = $this->columnName;
            $this->sortDirection = isset($values[self::PARAM_NAME_SORT_ORDER]) &&
                in_array(strtoupper($values[self::PARAM_NAME_SORT_ORDER]), [self::SORT_ASC, self::SORT_DESC], true) ?
                    strtoupper($values['sortOrder']) : $this->defaultDirection;

        } elseif($this->defaultDirection !== null) {
            $this->sortColumn = $this->columnName;
            $this->sortDirection = $this->defaultDirection;
        }

        if ($this->sortColumn) {
            $appliedValues[self::PARAM_NAME_SORT_BY] = $this->columnAlias;
        }

        if ($this->sortColumn && $this->sortDirection) {
            $appliedValues[self::PARAM_NAME_SORT_ORDER] = $this->sortDirection;
        }

        return $appliedValues;
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $builder
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function apply(QueryBuilder $builder)
    {
        if ($this->sortColumn) {
            $builder->addOrderBy($this->sortColumn, $this->sortDirection);
        }
        return $builder;
    }
}
