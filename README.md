# PHP Light SQL Parser Class

**Version:** 0.1.93 beta

**Github:** https://github.com/marcocesarato/PHP-Light-SQL-Parser-Class

**Author:** Marco Cesarato

## Description

This class can parse SQL to get query type, tables, field values, etc..

It takes an string with a SQL statements and parses it to extract its different components.

Currently the class can extract the SQL query method, the names of the tables involved in the query and the field values that are passed as parameters.
This parser is pretty light respect phpsqlparser or others php sql parser.

## Requirements

- php 4+

## Usage

```php
$lsp = new LightSQLParser("UPDATE Customers as ae SET ContactName = 'Alfred Schmidt', City= 'Frankfurt' WHERE CustomerID = 1;");
```

OR

```php
$lsp = new LightSQLParser();
$lsp->setQuery("UPDATE Customers as ae SET ContactName = 'Alfred Schmidt', City= 'Frankfurt' WHERE CustomerID = 1;");
```

### Method
How retrieve query method:
```php
$lsp->method();
```
Output
```
string(6) "UPDATE"
```

### Tables
How retrieve query tables:
```php
$lsp->tables();
```
Output
```
array(1) {
  [0]=>
  string(9) "Customers"
}
```

### Fields
How retrieve query fields:
```php
$lsp->fields();
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

Methods


### LightSQLParser

| Method      | Parameters                          | Description                                        |
| ----------- | ----------------------------------- | -------------------------------------------------- |
| __construct |                                     | Constructor                                        |
| setQuery    |                                     | Set SQL Query string                               |
| method      | 	  param $query<br>	  return string | Get SQL Query method                               |
| fields      | 	  param $query<br>	  return array  | Get Query fields (at the moment only SELECTINSERTUPDATE) |
| table       | 	  param $query<br>	  return string | Get SQL Query First Table                          |
| tables      | 	  return array                     | Get SQL Query Tables                               |


