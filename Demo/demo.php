<?php
include('../lightsqlparser.class.php');

header("Content-Type: text/plain");

echo '========= Light SQL Parser DEMO =========' . PHP_EOL;

echo PHP_EOL . '### UPDATE ###' . PHP_EOL;

$lsp = new LightSQLParser("UPDATE Customers as ae 
SET ContactName = 'Alfred Schmidt', City= 'Frankfurt' 
WHERE CustomerID = 1;");

// OR

/*
$lsp = new LightSQLParser();
$lsp->setQuery("UPDATE Customers as ae
SET ContactName = 'Alfred Schmidt', City= 'Frankfurt'
WHERE CustomerID = 1;");
*/

echo PHP_EOL . 'METHOD' . PHP_EOL;
var_dump($lsp->method());

echo PHP_EOL . 'TABLES' . PHP_EOL;
var_dump($lsp->tables());

echo PHP_EOL . 'FIELDS' . PHP_EOL;
var_dump($lsp->fields());

echo PHP_EOL . '### SELECT ###' . PHP_EOL;

$lsp->setQuery("SELECT surname, given_names, title FROM Person 
  JOIN Author on person.ID = Author.personID 
  JOIN Book on Book.ID = Author.publicationID 
UNION ALL 
SELECT surname, given_names, title FROM Person 
  JOIN Author on person.ID = Author.personID 
  JOIN Article on Article.ID = Author.publicationID");

echo PHP_EOL . 'METHOD' . PHP_EOL;
var_dump($lsp->method());

echo PHP_EOL . 'TABLES' . PHP_EOL;
var_dump($lsp->tables());

echo PHP_EOL . 'FIELDS' . PHP_EOL;
var_dump($lsp->fields());

echo PHP_EOL . '### INSERT ###' . PHP_EOL;

$lsp->setQuery("INSERT INTO Customers (CustomerName, ContactName, Address, City, PostalCode, Country) 
VALUES ('Cardinal', 'Tom B. Erichsen', 'Skagen 21', 'Stavanger', '4006', 'Norway');");

echo PHP_EOL . 'METHOD' . PHP_EOL;
var_dump($lsp->method());

echo PHP_EOL . 'TABLES' . PHP_EOL;
var_dump($lsp->tables());

echo PHP_EOL . 'FIELDS' . PHP_EOL;
var_dump($lsp->fields());
