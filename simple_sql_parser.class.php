<?php
/**
 * Simple SQL Parser Class
 * @author Marco Cesarato <cesarato.developer@gmail.com>
 */
class SimpleSQLParser {

	public $query = '';

	public function __construct($query) {
		$this->query = $query;
	}

	/**
	 * Get SQL Query method
	 * @param $query
	 * @return string
	 */
	public function method(){
		$methods = array('SELECT','INSERT','UPDATE','DELETE','RENAME','SHOW','SET','DROP','CREATE INDEX','CREATE TABLE','EXPLAIN','DESCRIBE','TRUNCATE');
		$query = preg_replace('#\/\*[\s\S]*?\*\/#','', $this->query);
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
	 * Get SQL Query Tables
	 * @param $query
	 * @return array
	 */
	public function tables(){
		$query = $this->query;
		$tables = array();
		do {
			$match = false;
			$table = self::_table($query);
			if(!empty($table)){
				$tables[] = $table;
				$query = preg_replace('#('.$table.'([\s]+(AS[\s]+)?[\w\_]+)?[\s]*(,?))#i','',$query);
				$match = true;
			}
		} while($match);
		return $tables;
	}

	/**
	 * Get SQL Query First Table
	 * @param $query
	 * @return string
	 */
	private static function _table($query){
		$query = preg_replace('#\/\*[\S\s]*?\*\/#','', $query);
		$patterns = array(
			'#[\S\s]+[\s]+FROM[\s]+([\w\_]+)[\s]*(WHERE|JOIN|GROUP BY|ORDER BY|OPTION|LEFT|INNER|RIGHT|OUTER|UNION|SET|HAVING|[\(]|[\)])?[\S\s]*#i',
			'#[\S\s]*UPDATE[\s]+([\w\_]+)[\s]*(SET|[\(]|[\)])?[\S\s]*#i',
			'#[\S\s]*INSERT[\s]+INTO[\s]+([\w\_]+)[\s]*(VALUES|SELECT|[\(]|[\)])?[\S\s]*#i',
			'#[\S\s]*TABLE[\s]+([\w\_]+)[\s]*(WHERE|ORDER BY|OPTION|[\(]|[\)])?[\S\s]*#i'
		);
		foreach($patterns as $pattern){
			$table = preg_replace($pattern,'$1', $query);
			if(!empty(trim($table)) && $table != $query) return trim($table);
		}
		return '';
	}

	/**
	 * Get SQL SELECT fields
	 * @param $query
	 * @return array
	 */
	public function selectedFields(){
		$fields = array();
		if($this->method() == 'SELECT') {
			preg_match('#SELECT[\s]+([\S\s]*)[\s]+FROM#i', $this->query, $matches);
			if (!empty($matches[1])) {
				$match = trim($matches[1]);
				$match = explode(',', $match);
				foreach ($match as $field) {
					$field = preg_replace('#([\s]+(AS[\s]+)?[\w\_]+)#i', '', trim($field));
					$fields[] = $field;
				}
			}
		}
		return $fields;
	}
}