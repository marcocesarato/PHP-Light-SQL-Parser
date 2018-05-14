<?php
/**
 * Get SQL Query method
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 * @param $query
 * @return string
 */
function sql_query_method($query){
    $methods = array('SELECT','INSERT','UPDATE','DELETE','REPLACE','RENAME','SHOW','SET','DROP','CREATE INDEX','CREATE TABLE','EXPLAIN','DESCRIBE','TRUNCATE');
    $query = preg_replace('#\/\*[\s\S]*?\*\/#','', $query);
    $query = preg_replace('#;(?:(?<=["\'];)|(?=["\']))#', '', $query);
    $query = explode(';', $query);
    foreach($query as $_q){
        foreach($methods as $method) {
            $_method = str_replace(' ', '[\s]+', $method);
            if(preg_match('#^[\s]*'.$_method.'[\s]+#i', $_q)){
                return $method;
            }
        }
    }
    return '';
}
/**
 * Get SQL Query First Table
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 * @param $query
 * @return string
 */
function sql_query_table($query){
    $query = preg_replace('#\/\*[\S\s]*?\*\/#','', $query);
    $patterns = array(
        '#[\S\s]+[\s]+FROM[\s]+([\w\_]+)[\s]*(WHERE|JOIN|GROUP BY|ORDER BY|OPTION|LEFT|INNER|RIGHT|OUTER|UNION|SET|HAVING|[\(]|[\)])?[\S\s]*#i',
        '#[\S\s]*UPDATE[\s]+([\w\_]+)[\s]*(SET|[\(]|[\)])?[\S\s]*#i',
        '#[\S\s]*INSERT[\s]+INTO[\s]+([\w\_]+)[\s]*(VALUES|SELECT|[\(]|[\)])?[\S\s]*#i'
    );
    foreach($patterns as $pattern){
        $table = preg_replace($pattern,'$1', $query);
        if(!empty(trim($table)) && $table != $query) return trim($table);
    }
    return '';
}
/**
 * Get SQL Query Tables
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 * @param $query
 * @return array
 */
function sql_query_tables($query){
    $tables = array();
    do {
        $match = false;
        $table = sql_query_table($query);
        if(!empty($table)){
            $tables[] = $table;
            $query = preg_replace('#('.$table.'([\s]+(AS[\s]+)?[\w\_]+)?[\s]*(,?))#i','',$query);
            $match = true;
        }
    } while($match);
    return $tables;
}
/**
 * Get SQL SELECT fields
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 * @param $query
 * @return array
 */
function sql_query_fields_selected($query){
    $fields = array();
    preg_match('#SELECT[\s]+([\S\s]*)[\s]+FROM#i', $query, $matches);
    if(!empty($matches[1])){
        $match = trim($matches[1]);
        $match = explode(',', $match);
        foreach($match as $field){
            $field = preg_replace('#([\s]+(AS[\s]+)?[\w\_]+)#i','', trim($field));
            $fields[] = $field;
        }
    }
    return $fields;
}