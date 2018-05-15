<?php
/**
 * Light SQL Parser Class
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 */

class LightSQLParser {

	// Public
	public $query = '';

	// Private
	private static $connectors = array('ON','AS','LIMIT','WHERE','JOIN','GROUP BY','ORDER BY','OPTION','LEFT','INNER','RIGHT','OUTER','SET','HAVING','VALUES','SELECT','INSERT','UPDATE','DELETE','RENAME','SHOW','SET','DROP','CREATE INDEX','CREATE TABLE','EXPLAIN','DESCRIBE','TRUNCATE','ALTER','INTO','\(','\)');

	/**
	 * Constructor
	 */
	public function __construct($query) {
		$this->query = $query;
	}

	/**
	 * Get SQL Query method
	 * @param $query
	 * @return string
	 */
	public function method($query = null){
		$methods = array('SELECT','INSERT','UPDATE','DELETE','RENAME','SHOW','SET','DROP','CREATE INDEX','CREATE TABLE','EXPLAIN','DESCRIBE','TRUNCATE','ALTER');
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

		$connectors = self::$connectors;
		if (($key = array_search('AS', $connectors)) !== false) {
			unset($connectors[$key]);
		}
		$connectors = implode('|', $connectors);

		foreach($queries as $query) {
			do {
				$match = false;
				$table = $this->_table($query);
				if (!empty($table)) {
					$tables[] = $table;
					$query = preg_replace('#(' . $table . '([\s]+(?!'.$connectors.')(AS[\s]+)?[\w]+)?[\s]*(,?))#i', '', $query);
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
			$method = $this->method($query);
			switch ($method){
				case 'SELECT':
					preg_match('#SELECT[\s]+([\S\s]*)[\s]+FROM#i', $query, $matches);
					if (!empty($matches[1])) {
						$match = trim($matches[1]);
						$match = explode(',', $match);
						foreach ($match as $field) {
							$field = preg_replace('#([\s]+(AS[\s]+)?[\w\.]+)#i', '', trim($field));
							$fields[] = $field;
						}
					}
					break;
				case 'INSERT':
					preg_match('#INSERT[\s]+INTO[\s]+([\w\.]+([\s]+(AS[\s]+)?[\w\.]+)?[\s]*)\(([\S\s]*)\)[\s]+VALUES#i', $query, $matches);
					if (!empty($matches[4])) {
						$match = trim($matches[4]);
						$match = explode(',', $match);
						foreach ($match as $field) {
							$field = preg_replace('#([\s]+(AS[\s]+)?[\w\.]+)#i', '', trim($field));
							$fields[] = $field;
						}
					}
					break;
				case 'UPDATE':
					preg_match('#UPDATE[\s]+([\w\.]+([\s]+(AS[\s]+)?[\w\.]+)?[\s]*)SET([\S\s]*)[\s]+(WHERE|[\;])?#i', $query, $matches);
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
		$connectors = implode('|', self::$connectors);
		$patterns = array(
			'#[\S\s]*INSERT[\s]+INTO[\s]+(?!'.$connectors.')([\w]+)([\s]+('.$connectors.'))?[\S\s]*#i',
			'#[\S\s]*UPDATE[\s]+(?!'.$connectors.')([\w]+)([\s]+('.$connectors.'))?[\S\s]*#i',
			'#[\S\s]+[\s]+JOIN[\s]+(?!'.$connectors.')([\w]+)([\s]+('.$connectors.'))?[\S\s]*#i',
			'#[\S\s]+[\s]+FROM[\s]+(?!'.$connectors.')([\w]+)([\s]+('.$connectors.'))?[\S\s]*#i',
			'#[\S\s]*TABLE[\s]+(?!'.$connectors.')([\w]+)([\s]+('.$connectors.'))?[\S\s]*#i'
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