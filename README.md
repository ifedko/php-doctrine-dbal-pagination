# PHP DOCTRINE DBAL pagination

![](https://travis-ci.org/ifedko/php-doctrine-dbal-pagination.svg?branch=master)

## The goal

Everyone faces the task when it's required to get list of items by customizable filters,
to sort by customizable parameters and paginate these items.
This library helps to do it.

## Usage


### GETTING STARTED

Examples work with table `users` (`id`, `is_active`, `name`, `created_at`).

At first create your builder. It's necessary to define base query there:

```
<?php

namespace Foo\Bar;

use Ifedko\DoctrineDbalPagination\ListBuilder;

class MyListBuilder extends ListBuilder
{
	protected function baseQuery()
	{
	    $builder = $this->getQueryBuilder();
        $builder
            ->select(['u.id, u.name, u.created_at'])
            ->from('users', 'u')
            ->where('u.is_active IS TRUE');

        return $queryBuilder;
	}
}
```

Then use the builder:

```
<?php

namespace Foo\Bar;

use Ifedko\DoctrineDbalPagination\ListPagination;
use Foo\Bar\MyListBuilder;

$dbConnection = \Doctrine\DBAL\DriverManager::getConnection(['url' => 'mysql://dbuser:dbpasswd@127.0.0.1/dbname']);
$limit = 10;
$offset = 20;
$parameters = [];

$listBuilder = new MyListBuilder(
	$dbConnection
);
$listBuilder->configure($parameters);

$listPagination = new ListPagination($listBuilder);
$listPage = $listPagination->get($limit, $offset);
```

where

`$limit` is a count of items on page (`20` by default)

`$offset` is a number of start row (`0` by default)

### FILTERING

Add `configureFilters` method to your `MyListBuilder` class to use customizable filtering.

This examples explains how to turn on filters by certain fields: `id`, `name`, `created_at`
```
...

protected function configureFilters($parameters)
{
    $mapAvailableFilterByParameter = [
        'id' => new EqualFilter('id', \PDO::PARAM_INT),
        'name' => new EqualFilter('name', \PDO::PARAM_STR),
        'created_at_from' => new GreaterThanOrEqualFilter('user.created_at'),
        'created_at_to' => new LessThanOrEqualFilter('user.created_at')
    ];

    /* @var $filter FilterInterface */
    foreach ($mapAvailableFilterByParameter as $parameterName => $filter) {
        if (isset($parameters[$parameterName])) {
            $filter->bindValues($parameters[$parameterName]);
            $this->filters[] = $filter;
        }
    }

    return $this;
}
...
```

where

`parameters` is array which can contain parameters for filter (see using the builder above)

 `EqualFilter`, `GreaterThanOrEqualFilter` and `LessThanOrEqualFilter` are some of available filters (see below about all filters)


For example,
```
$parameters = [
    'id' => 1,
    'title' => 'name'
];
```

### SORTING

Add `configureSorting` method to your `MyListBuilder` class:

```
...
use Ifedko\DoctrineDbalPagination\Sorting\ByColumn;

...

protected function configureSorting($parameters)
{
    $this->sortUsing(new ByColumn('id', 'user_id'), $parameters);
    $this->sortUsing(new ByColumn('name', 'name'), $parameters);
    $this->sortUsing(new ByColumn('from', 'user.created_at'), $parameters);
    $this->sortUsing(new ByColumn('to', 'user.created_at'), $parameters);

    return $this;
}
...
```

where

`parameters` is array which can contain parameters for sorting (see using the builder above)

For example,
```
$parameters = [
    'sortBy' => 'name',
    'orderBy' => 'DESC'
];
```

### FILTERS

* DateRangeFilter
* EqualFilter
* GreaterThanOrEqualFilter
* LessThanOrEqualFilter
* LikeFilter
* MultipleEqualFilter
* MultipleLikeFilter

## MultipleLikeFilter

This filter supports complex search queries, like substrings separated by a space. It narrows results
when more search words is provided. Also negotiation is possible, like '-word'.

example:

search: `bla`
results (3):

c1 | c2
--- | ---
**bla** | first |
**bla** | second |
**bla** | final |


search: `bla fi`
results (2):

c1 | c2
--- | ---
**bla** | **fi**rst
**bla** | **fi**nal

search: `bla fi -final`
results (1):

c1 | c2
--- | ---
**bla** | **fi**rst

### Options

It is possible to define the options:

* operator - comparison operator, ILIKE, for example
* matchFromStart - array of columns to do match from start of the string, `col like 'substring%'` for example
