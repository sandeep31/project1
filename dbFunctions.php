<?php

class DB {

    private $conn;
	
	private function __construct()
    {
		echo "In DB Construct";
		//$this->conn = $conn;
    }
	
	/*private function query($sql)
	{
		$stmt = sqlsrv_query( $this->conn, $sql);
		
		if( $stmt === false ) {
			 die( print_r( sqlsrv_errors(), true));
		} else {
			return $stmt;
		}
	}
	
	private function num_rows($stmt)
	{
		$row_count = sqlsrv_num_rows( $stmt );
		
		return $row_count;
	}
	
	
	private function fetch_object($stmt)
	{
		$obj = sqlsrv_fetch_object( $stmt );
		
		return $obj;
	}

	private function fetch_assoc($stmt)
	{
		$obj = sqlsrv_fetch_object( $stmt );
		
		$data = $this->object_to_array($obj);
		 
		return $data;
		
	}

	private function object_to_array($data)
	{
		if (is_array($data) || is_object($data))
		{
			$result = array();
			foreach ($data as $key => $value)
			{
				$result[$key] = object_to_array($value);
			}
			return $result;
		}
		return $data;
	}*/
	
}



?>