<?php
/**
 * Light SQL Parser Class
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 */

class LightSQLParser {

	public $query = '';

	public function __construct($query) {
		$this->query = $query;
	}

	/**
	 * Get SQL Query method
	 * @param $query
	 * @return string
	 */
	public function method($query = null){
		$methods = array('SELECT','INSERT','UPDATE','DELETE','RENAME','SHOW','SET','DROP','CREATE INDEX','CREATE TABLE','EXPLAIN','DESCRIBE','TRUNCATE', 'ALTER');
		$queries = empty($query) ? $this->_queries() : array($query);
		foreach($queries as $query){
			foreach($methods as $method) {
				$_method = str_replace(' ', '[\s]+', $method);
				if(preg_match('#^[\s]*'.$_method.'[\s]+#i', $query)){
					return $method;
				}
			}
		}
		return '';
	}

	/**
	 * Get SQL Query Tables
	 * @param $query
	 * @return array
	 */
	public function tables(){
		$tables = array();
		$queries = $this->_queries();
		foreach($queries as $query) {
			do {
				$match = false;
				$table = $this->_table($query);
				if (!empty($table)) {
					$tables[] = $table;
					$query = preg_replace('#(' . $table . '([\s]+(AS[\s]+)?[\w\_]+)?[\s]*(,?))#i', '', $query);
					$match = true;
				}
			} while ($match);
		}
		return array_unique($tables);
	}

	/**
	 * Get Query fields (at the moment only SELECT/INSERT/UPDATE)
	 * @param $query
	 * @return array
	 */
	public function fields(){
		$fields = array();
		$queries = $this->_queries();
		foreach($queries as $query) {
			$method = $this->method($method);
			switch ($method){
				case 'SELECT':
					preg_match('#SELECT[\s]+([\S\s]*)[\s]+FROM#i', $query, $matches);
					if (!empty($matches[1])) {
						$match = trim($matches[1]);
						$match = explode(',', $match);
						foreach ($match as $field) {
							$field = preg_replace('#([\s]+(AS[\s]+)?[\w\_]+)#i', '', trim($field));
							$fields[] = $field;
						}
					}
					break;
				case 'INSERT':
					preg_match('#INSERT[\s]+INTO[\s]+([\w\_]+([\s]+(AS[\s]+)?[\w\_]+)?[\s]*)\(([\S\s]*)\)[\s]+VALUES#i', $query, $matches);
					if (!empty($matches[4])) {
						$match = trim($matches[4]);
						$match = explode(',', $match);
						foreach ($match as $field) {
							$field = preg_replace('#([\s]+(AS[\s]+)?[\w\_]+)#i', '', trim($field));
							$fields[] = $field;
						}
					}
					break;
				case 'UPDATE':
					preg_match('#UPDATE[\s]+([\w\_]+([\s]+(AS[\s]+)?[\w\_]+)?[\s]*)SET([\S\s]*)[\s]+(WHERE|[\;])?#i', $query, $matches);
					if (!empty($matches[4])) {
						$match = trim($matches[4]);
						$match = explode(',', $match);
						foreach ($match as $field) {
							$field = preg_replace('#([\s]*\=[\s]*[\S\s]+)#i', '', trim($field));
							$fields[] = $field;
						}
					}
					break;
			}
		}
		return array_unique($fields);
	}

	/**
	 * Get SQL Query First Table
	 * @param $query
	 * @return string
	 */
	private function _table($query){
		$query = preg_replace('#\/\*[\S\s]*?\*\/#','', $query);
		$patterns = array(
			'#[\S\s]+[\s]+JOIN[\s]+([\w\_]+)[\s]+(ON|AS|LIMIT|WHERE|JOIN|GROUP BY|ORDER BY|OPTION|LEFT|INNER|RIGHT|OUTER|UNION|SET|HAVING|[\(]|[\)]|[\;])?[\S\s]*#i',
			'#[\S\s]+[\s]+FROM[\s]+([\w\_]+)[\s]+(AS|LIMIT|WHERE|JOIN|GROUP BY|ORDER BY|OPTION|LEFT|INNER|RIGHT|OUTER|UNION|SET|HAVING|[\(]|[\)]|[\;])?[\S\s]*#i',
			'#[\S\s]*UPDATE[\s]+([\w\_]+)[\s]+(SET|[\(]|[\)]|[\;])?[\S\s]*#i',
			'#[\S\s]*INSERT[\s]+INTO[\s]+([\w\_]+)[\s]+(VALUES|SELECT|[\(]|[\)]|[\;])?[\S\s]*#i',
			'#[\S\s]*TABLE[\s]+([\w\_]+)[\s]+(WHERE|ORDER BY|OPTION|[\(]|[\)]|[\;])?[\S\s]*#i'
		);
		foreach($patterns as $pattern){
			$table = preg_replace($pattern,'$1', $query);
			if(!empty(trim($table)) && $table != $query) return trim($table);
		}
		return '';
	}

	/**
	 * Get all queries
	 * @return array
	 */
	private function _queries(){
		$queries = preg_replace('#\/\*[\s\S]*?\*\/#','', $this->query);
		$queries = preg_replace('#;(?:(?<=["\'];)|(?=["\']))#', '', $queries);
		$queries = preg_replace('#[\s]*UNION([\s]+ALL)?[\s]*#', ';', $queries);
		$queries = explode(';', $queries);
		return $queries;
	}
}