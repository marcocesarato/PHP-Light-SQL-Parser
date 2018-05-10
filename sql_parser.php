<?php
/**
 * Get SQL Query method
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 * @param $query
 * @return string
 */
function sql_query_method($query){
	$query = preg_replace('#\/\*[\s\S]*?\*\/#','', $query);
	$method = 'UNKOWN';
	if(preg_match('/^[\s]*SELECT[\s]+/i', $query)){
		return 'SELECT';
	} else if(preg_match('/^[\s]*INSERT[\s]+/i', $query)){
		return 'INSERT';
	} else if(preg_match('/^[\s]*UPDATE[\s]+/i', $query)){
		return 'UPDATE';
	} else if(preg_match('/^[\s]*DELETE[\s]+/i', $query)) {
		return 'DELETE';
	} else if(preg_match('/^[\s]*SHOW[\s]+/i', $query)) {
		return 'SHOW';
	}
	return $method;
}

/**
 * Get SQL Query First Table
 * @param $query
 * @return boolean|string
 */
function sql_query_table($query){
	$query = preg_replace('#\/\*[\S\s]*?\*\/#','', $query);
	$table = preg_replace('#[\S\s]+[\s]+FROM[\s]+([\w]+)[\s]*[\S\s]*#i','$1', $query);
	if(empty(trim($table)) || $table == $query){
		$table = preg_replace('#[\S\s]*UPDATE[\s]+([\w]+)[\s]*[\S\s]*#i','$1', $query);
		if(empty(trim($table)) || $table == $query){
			$table = preg_replace('#[\S\s]*INSERT[\s]+INTO[\s]+([\w]+)[\s]*[\S\s]*#i','$1', $query);
			if(empty(trim($table)) || $table == $query){
				$table = preg_replace('#[\S\s]*DELETE[\s]+([\w]+)[\s]*[\S\s]*#i','$1', $query);
				if(empty(trim($table)) || $table == $query) return false;
			}
		}
	}
	return trim($table);
}