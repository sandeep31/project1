<?php

	include_once('Config.php');

	class DB
	{
		private $Server;
		private $User;
		private $Pwd;
		private $Name;
		private $db;
		private $objDBConn;
		private $RsId;
		private $RecordSet;
		private $SqlQuery;
		

		public function __construct()
		{
		
		  /////// DB Connection Details ///////////////
			
				 $this->Server 	= 'localhost' ; //SQL_HOSTNAME;
				 $this->User 	= 'root'	;
				 $this->Pwd 	= '';
				 $this->Name 	= 'dhirajgv_dserver';
	
				 $this->db_connect();
		}

		public function db_connect()
		{

			$this->objDBConn = mysqli_connect($this->Server, $this->User, $this->Pwd , $this->Name) or die( "Failed to connect to MySQL: " . mysqli_connect_error() );
			//$this->db = mysqli_select_db($this->objDBConn , ) or die("cannot select".mysqli_error());
		}

		public function query($strQuery)
		{
			$this->SqlQuery = $strQuery;
			
			$rsId = mysqli_query( $this->objDBConn , $this->SqlQuery);
			
			$this->rsId = $rsId;
			
			if($rsId)
			{
				return $this->rsId;
			} else {
				$this->error();
			}
		}
		public function fetch_array($rst)
		{
 			if($rst) { 
				$this->RecordSet  = mysqli_fetch_array($rst);
				return $this->RecordSet;
			} else { $this->error(); }
		}
		
		public function fetch_row($rst)
		{
 			if($rst) {  
				$this->RecordSet  = mysqli_fetch_row($rst);
				return $this->RecordSet;
			} else { $this->error(); }
		}
		
		
		public function fetch_assoc($rst)
		{
			if($rst) { 
				$this->RecordSet  = mysqli_fetch_assoc($rst);
				return $this->RecordSet;
			} else { $this->error(); }
		}

		public function num_row($rst)
		{
			if($rst) {  
				$num = mysqli_num_rows($rst);
				return $num;
			} else { $this->error(); }
		}
		
		public function num_rows($rst)
		{
			if($rst) {  
				$num = mysqli_num_rows($rst);
				return $num;
			} else { $this->error(); }
		}
		
		public function affected_rows()
		{
			$num = mysqli_affected_rows( $this->objDBConn );
			return $num;
		}
		
		public function insert_id()
		{
			 
				$id = mysqli_insert_id( $this->objDBConn );
				
				return $id;
			 
		}
		
		public function real_escape_string($string)
		{
 			if(phpversion() >= '4.3')
  			{
				$string = mysqli_real_escape_string( $this->objDBConn , $string);
				
			} else {
				
				$string = mysqli_escape_string( $this->objDBConn , $string);
			}
			
			return $string;
		}
		
		public function error()
		{
			$errorMsg = mysqli_error( $this->objDBConn );
			
			if(!empty($errorMsg))
			{
				echo "<div class='errormsg'><strong>Mysql Error: </strong>$errorMsg</div>";
				echo "<div>".$this->SqlQuery."</div>";
				exit;
			}
		}
		
		public function free_result($rst)
		{
			 
			 mysqli_free_result( $rst );
			 
		}
		
		public function free($rst)
		{
			 
			 mysqli_free_result( $rst );
			 
		}

		public function __destruct()
		{
			if($this->objDBConn)
			{
				 
				foreach ($this as $key => $value) 
				 { 
					 unset($this->key); 
				 } 
			}
		}
		
	}
	
	
	
	
class Mysql
{
 	public $objDB;
	
	public function __construct($objDB)
	{
		$this->objDB = $objDB;
	
	
	}
	
	public function setSqlData($dataArray , $dbTableName, $action='insert', $idFieldName='', $idFieldValue='')
	{
	
		 
		if(is_array($dataArray))
		{
			foreach($dataArray as $key=>$value)
			{
				switch($action)
				{
					case "insert":	
					
						$fieldName[] = $key;
						
						$fieldValue[] = "'$value'";
					
					
					break;
					
					case "update":
					
						$fieldNameWithDate[] = "$key = '$value' ";
					
					break;
				
				}//end switch.
			
			}//end foreach.
			
			
			switch($action)
			{
				case "insert":	
				
					$strFieldName  = join(',', $fieldName);
					$strFieldValue = join(',', $fieldValue);
					
					$sqlQuery = "INSERT INTO ".$dbTableName." ( $strFieldName ) VALUES ( $strFieldValue )";
				
				break;
				
				case "update":
					
					$strUpdateFieldNameValue = join(',', $fieldNameWithDate);
					
				 	$sqlQuery = "UPDATE ".$dbTableName." SET $strUpdateFieldNameValue WHERE $idFieldName = '$idFieldValue' ";
				
				break;
			
			}//end switch.
			
			 
			$rec = $this->objDB->query($sqlQuery);
			
			return $rec;
			
		}//end if.
	
	
	}//end method function.
	 


	////
	// Attr: resultType: RecordArray / Record / Count
	//

	public function getSqlData( $sqlQuery , $resultType='RecordArray' )
	{

		if($sqlQuery != "")
		{
			$rec = $this->objDB->query($sqlQuery);
			
			if($rec)
			{
				if($num=$this->objDB->num_rows($rec))
				{
					switch($resultType)
					{
						//return only count of record selected
						case "Count":
						
							return $num;
							
						break;
						
						//Return single record
						case "Record":
						
						return	$row = $this->objDB->fetch_assoc($rec);
						
						break;
						
						//return multiple records in array format
						case "RecordArray":
						
							while($row = $this->objDB->fetch_assoc($rec))
							{
								$rowArray[] = $row;
							
							}//end while.
						
							return $rowArray;
							
						break;
					
					}//end switch.
				
				}//end if num.
				else
				{
					//echo "<div class='error'>&Delta; No record found.</div>";
				
				}//end else.
			}//end if.
			else
			{
				echo "<div class='error'>&Delta; Sql query error.</div>";
			
			}//end else.
		}//end if.

	}//end method.
	
	
	public function isUniqueData( $PrimaryKeyName, $fieldDataArray , $tableName )
	{

		if(is_array($fieldDataArray))
		{
			foreach($fieldDataArray as $fieldName=>$fieldValue)
			{
				$whereArray[] = " $fieldName = '$fieldValue' ";
			
			}//end foreach.
			
			$where = join(" AND " , $whereArray);
			
		 	$sqlQuery = "SELECT count($PrimaryKeyName) FROM $tableName WHERE $where ";
	 	
			
			return  $this->getSqlData( $sqlQuery , $resultType='Count' );
			
			 
			
		}//end if.
		else
		{
			echo "<div>&Delta; Invalid argument porvide in function.</div>";
		}
 
	}//end method.
	


}	
	
	
?>