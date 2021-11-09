<?php


Class DB
{
	private $connection;
    private $result;
	private $statement;
    public $sql;

	function __construct()
	{
		$this->connection = new mysqli('localhost', 'root', '', 'api');
		$this->connection->set_charset('utf8');
		if ($this->connection->connect_error)
			die('Connect Error (' . $this->connection->connect_errno . ') ' . $this->connection->connect_error);
	}

	function refValues($arr)
	{
		//Reference is required for PHP 5.3+
		if (strnatcmp(phpversion(),'5.3') >= 0) {
	        	$refs = array();
	        	foreach($arr as $key => $value)
	            		$refs[$key] = &$arr[$key];
	        	return $refs;
	    	}
	   	return $arr;
	}

	public function interpolateQuery($query, $params) {
    $keys = array();
    $values = $params;

    # build a regular expression for each parameter
    foreach ($params as $key => $value) {
        if (is_string($key)) {
            $keys[] = '/:'.$key.'/';
        } else {
            $keys[] = '/[?]/';
        }

        if (is_array($value))
            $values[$key] = implode(',', $value);

        if (is_null($value))
            $values[$key] = 'NULL';
    }
	// Walk the array to see if we can add single-quotes to strings
    array_walk($values, create_function('&$v, $k', 'if (!is_numeric($v) && $v!="NULL") $v = "\'".$v."\'";'));

    $query = preg_replace($keys, $values, $query, 1, $count);

    return $query;
}

	function query($sql, $params = array())
	{
		$this->statement = $this->connection->prepare($sql);
		$bind = False;

		if (!$this->statement)
			return false;

		if (!empty($params))
		{
			$paramTypes = '';
			$paramRefs = array();
			$paramRefs[0] = '';
			foreach ($params as $i=>$param)
			{
				switch (gettype($param))
				{
					case 'NULL':
					case 'string':
						$paramTypes .= 's';
						break;
					case 'integer':
						$paramTypes .= 'i';
						break;
					case 'blob':
						$paramTypes .= 'b';
						break;
					case 'double':
						$paramTypes .= 'd';
						break;
				}
				array_push($paramRefs, filter_var($params[$i], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
			}
			$paramRefs[0] = $paramTypes;
			//print_r($paramRefs);
			$bind = call_user_func_array(array($this->statement, "bind_param"), $this->refValues($paramRefs));
			if ($bind) {
				$this->statement->execute();
				mysqli_stmt_store_result($this->statement);
			}
			else {
				throw new Exception($this->statement->error);
			}
		}
		else {
			$this->statement->execute();
			mysqli_stmt_store_result($this->statement);
		}

		return true;
	}

	function error() {
		return $this->connection->error;
	}

	function fetch()
	{
		if (!$this->statement)
			return false;

		$parameters = array();
		$results = array();

		$meta = $this->statement->result_metadata();

		$row = array();
		while ($field = $meta->fetch_field()) {
			$row[$field->name] = NULL;
			$parameters[] = &$row[$field->name];
		}

        call_user_func_array(array($this->statement, "bind_result"),$parameters);

		while ($this->statement->fetch()) {
			$x = array();
			foreach ($row as $key => $val) {
				$x[$key] = $val;
			}
			array_push($results, $x);
		}
		return $results;
	}

	function getInsertId()
	{
		if (!$this->statement)
			return false;

		return $this->connection->insert_id;
	}

	function getAffectedRows()
	{
		if (!$this->statement)
			return false;

		return $this->statement->affected_rows;
	}

	function getNumRows()
	{
		if (!$this->statement)
			return false;

		return $this->statement->num_rows;
	}

	function getConnection()
	{
		return $this->connection;
	}

	function transaction() {
		mysqli_autocommit($this->connection, FALSE);
	}

	function rollback() {
		mysqli_rollback($this->connection);
	}

	function commit() {
		mysqli_commit($this->connection);
		mysqli_autocommit($this->connection, TRUE);
	}

	function __destruct()
	{
		$this->connection->close();
	}
}
?>

