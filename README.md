# PHP Light SQL Parser Class

**Version:** 0.2.105 beta

**Github:** https://github.com/marcocesarato/PHP-Light-SQL-Parser-Class

**Author:** Marco Cesarato

## Description

This class can parse SQL to get query type, tables, field values, etc..

It takes an string with a SQL statements and parses it to extract its different components.

Currently the class can extract the SQL query method, the names of the tables involved in the query and the field values that are passed as parameters.
This parser is pretty light respect phpsqlparser or others php sql parser.

## Requirements

- php 4+

## Install

### Composer
1. Install composer
2. Type `composer require marcocesarato/sqlparser`
4. Enjoy

## Usage

```php
$parser = new LightSQLParser("UPDATE Customers AS alias SET ContactName = 'Marco Cesarato', City = 'Milan' WHERE ID = 1;");
```

OR

```php
$parser = new LightSQLParser();
$parser->setQuery("UPDATE Customers AS alias SET ContactName = 'Marco Cesarato', City = 'Milan' WHERE ID = 1;");
```

### Method
How to retrieve the query's method:
```php
$parser->getMethod();
```
Output
```
string(6) "UPDATE"
```

### Tables

How to retrieve the main the query's table:
```php
$parser->getTable();
```
Output
```
string(9) "Customers"
```


How to retrieve the query's tables:
```php
$parser->getAllTables();
```
Output
```
array(1) {
  [0]=>
  string(9) "Customers"
}
```

### Fields
How to retrieve the query's fields:
```php
$parser->getFields();
```
Output
```
array(2) {
  [0]=>
  string(11) "ContactName"
  [1]=>
  string(4) "City"
}
```

## Methods


### LightSQLParser

| Method      | Parameters                          | Description                                        |
| ----------- | ----------------------------------- | -------------------------------------------------- |
| __construct |                                     | Constructor                                        |
| setQuery    |                                     | Set SQL Query string                               |
| getQuery    |   return array                                  | Get SQL Query string                               |
| getAllQuery    |  return string                                   | Get SQL All Query string                               |
| getMethod      | 	  param $query<br>	  return string | Get SQL Query method                               |
| getFields      | 	  param $query<br>	  return array  | Get Query fields (at the moment only SELECTINSERTUPDATE) |
| getTable       | 	  param $query<br>	  return string | Get SQL Query First Table                          |
| getTables      | 	  return array                     | Get SQL Query Tables                               |
| getJoinTables      | 	  return array                     | Get SQL Query Join Tables                               |
| hasJoin      | 	  return bool                     | Return if has join tables                               |
| getSubQueries      | 	  return array                     | Get all SELECT subqueries                              |
| hasSubQueries      | 	  return bool                     | Return if has subqueries                              |


