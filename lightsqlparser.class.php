<?php
/**
 * Light SQL Parser Class
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link https://gist.github.com/marcocesarato/b4dccc2df9ac1447d2676c0ae96c6994
 * @version 0.1.76
 */
class LightSQLParser {
	// Public
	public $query = '';
	// Private
	protected static $connectors = array('OR', 'AND', 'ON', 'LIMIT', 'WHERE', 'JOIN', 'GROUP', 'ORDER', 'OPTION', 'LEFT', 'INNER', 'RIGHT', 'OUTER', 'SET', 'HAVING', 'VALUES', 'SELECT', '\(', '\)');
	protected static $connectors_imploded = '';
	/**
	 * Constructor
	 */
	public function __construct($query = '') {
		$this->query = $query;
		if(empty(self::$connectors_imploded))
			self::$connectors_imploded = implode('|', self::$connectors);
		return $this;
	}
	/**
	 * Set SQL Query string
	 */
	public function setQuery($query) {
		$this->query = $query;
		return $this;
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
				case 'CREATE TABLE':
					preg_match('#CREATE[\s]+TABLE[\s]+\w+[\s]+\(([\S\s]*)\)#i', $query, $matches);
					if (!empty($matches[1])) {
						$match = trim($matches[1]);
						$match = explode(',', $match);
						foreach ($match as $_field) {
							preg_match('#^w+#', trim($_field), $field);
							if (!empty($field[0])) {
								$fields[] = $field[0];
							}
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
	public function table(){
		$tables = $this->tables();
		return $tables[0];
	}
	/**
	 * Get SQL Query Tables
	 * @return array
	 */
	function tables(){
		$results = array();
		$queries = $this->_queries();
			foreach($queries as $query) {
			$patterns = array(
				'#[\s]+FROM[\s]+(([\s]*(?!'.self::$connectors_imploded.')[\w]+([\s]+(AS[\s]+)?(?!'.self::$connectors_imploded.')[\w]+)?[\s]*[,]?)+)#i',
				'#[\s]*INSERT[\s]+INTO[\s]+([\w]+)#i',
				'#[\s]*UPDATE[\s]+([\w]+)#i',
				'#[\s]+[\s]+JOIN[\s]+([\w]+)#i',
				'#[\s]+TABLE[\s]+([\w]+)#i'
			);
			foreach($patterns as $pattern){
				preg_match_all($pattern,$query, $matches, PREG_SET_ORDER);
				foreach ($matches as $val) {
					$tables = explode(',', $val[1]);
					foreach ($tables as $table) {
						$table = trim(preg_replace('#[\s]+(AS[\s]+)[\w\.]+#i', '', $table));
						$results[] = $table;
					}
				}
			}
		}
		return array_unique($results);
	}
	/**
	 * Get all queries
	 * @return array
	 */
	protected function _queries(){
		$queries = preg_replace('#\/\*[\s\S]*?\*\/#','', $this->query);
		$queries = preg_replace('#;(?:(?<=["\'];)|(?=["\']))#', '', $queries);
		$queries = preg_replace('#[\s]*UNION([\s]+ALL)?[\s]*#', ';', $queries);
		$queries = explode(';', $queries);
		return $queries;
	}
}